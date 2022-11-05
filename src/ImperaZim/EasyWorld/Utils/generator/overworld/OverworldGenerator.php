<?php

namespace ImperaZim\EasyWorld\Utils\generator\overworld;

use ImperaZim\EasyWorld\Utils\generator\Environment;
use ImperaZim\EasyWorld\Utils\generator\ground\DirtAndStonePatchGroundGenerator;
use ImperaZim\EasyWorld\Utils\generator\ground\DirtPatchGroundGenerator;
use ImperaZim\EasyWorld\Utils\generator\ground\GravelPatchGroundGenerator;
use ImperaZim\EasyWorld\Utils\generator\ground\GroundGenerator;
use ImperaZim\EasyWorld\Utils\generator\ground\MesaGroundGenerator;
use ImperaZim\EasyWorld\Utils\generator\ground\MycelGroundGenerator;
use ImperaZim\EasyWorld\Utils\generator\ground\RockyGroundGenerator;
use ImperaZim\EasyWorld\Utils\generator\ground\SandyGroundGenerator;
use ImperaZim\EasyWorld\Utils\generator\ground\SnowyGroundGenerator;
use ImperaZim\EasyWorld\Utils\generator\ground\StonePatchGroundGenerator;
use ImperaZim\EasyWorld\Utils\generator\noise\glowstone\PerlinOctaveGenerator;
use ImperaZim\EasyWorld\Utils\generator\noise\glowstone\SimplexOctaveGenerator;
use ImperaZim\EasyWorld\Utils\generator\overworld\biome\BiomeHeightManager;
use ImperaZim\EasyWorld\Utils\generator\overworld\biome\BiomeIds;
use ImperaZim\EasyWorld\Utils\generator\overworld\populator\OverworldPopulator;
use ImperaZim\EasyWorld\Utils\generator\overworld\populator\SnowPopulator;
use ImperaZim\EasyWorld\Utils\generator\utils\preset\GeneratorPreset;
use ImperaZim\EasyWorld\Utils\generator\utils\preset\SimpleGeneratorPreset;
use ImperaZim\EasyWorld\Utils\generator\utils\WorldOctaves;
use ImperaZim\EasyWorld\Utils\generator\VanillaBiomeGrid;
use ImperaZim\EasyWorld\Utils\generator\VanillaGenerator;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;

class OverworldGenerator extends VanillaGenerator{

	protected static array $ELEVATION_WEIGHT = [];

	protected static array $GROUND_MAP = [];

	private static function elevationWeightHash(int $x, int $z) : int{
		return ($x << 3) | $z;
	}

	private static function densityHash(int $i, int $j, int $k) : int{
		return ($k << 6) | ($j << 3) | $i;
	}

	public static function init() : void{
		self::setBiomeSpecificGround(new SandyGroundGenerator(), BiomeIds::BEACH, BiomeIds::COLD_BEACH, BiomeIds::DESERT, BiomeIds::DESERT_HILLS, BiomeIds::DESERT_MUTATED);
		self::setBiomeSpecificGround(new RockyGroundGenerator(), BiomeIds::STONE_BEACH);
		self::setBiomeSpecificGround(new SnowyGroundGenerator(), BiomeIds::ICE_PLAINS_SPIKES);
		self::setBiomeSpecificGround(new MycelGroundGenerator(), BiomeIds::MUSHROOM_ISLAND, BiomeIds::MUSHROOM_ISLAND_SHORE);
		self::setBiomeSpecificGround(new StonePatchGroundGenerator(), BiomeIds::EXTREME_HILLS);
		self::setBiomeSpecificGround(new GravelPatchGroundGenerator(), BiomeIds::EXTREME_HILLS_MUTATED, BiomeIds::EXTREME_HILLS_PLUS_TREES_MUTATED);
		self::setBiomeSpecificGround(new DirtAndStonePatchGroundGenerator(), BiomeIds::SAVANNA_MUTATED, BiomeIds::SAVANNA_PLATEAU_MUTATED);
		self::setBiomeSpecificGround(new DirtPatchGroundGenerator(), BiomeIds::MEGA_TAIGA, BiomeIds::MEGA_TAIGA_HILLS, BiomeIds::REDWOOD_TAIGA_MUTATED, BiomeIds::REDWOOD_TAIGA_HILLS_MUTATED);
		self::setBiomeSpecificGround(new MesaGroundGenerator(), BiomeIds::MESA, BiomeIds::MESA_PLATEAU, BiomeIds::MESA_PLATEAU_STONE);
		self::setBiomeSpecificGround(new MesaGroundGenerator(MesaGroundGenerator::BRYCE), BiomeIds::MESA_BRYCE);
		self::setBiomeSpecificGround(new MesaGroundGenerator(MesaGroundGenerator::FOREST), BiomeIds::MESA_PLATEAU_STONE, BiomeIds::MESA_PLATEAU_STONE_MUTATED);

		for($x = 0; $x < 5; ++$x){
			for($z = 0; $z < 5; ++$z){
				$sq_x = $x - 2;
				$sq_x *= $sq_x;
				$sq_z = $z - 2;
				$sq_z *= $sq_z;
				self::$ELEVATION_WEIGHT[self::elevationWeightHash($x, $z)] = 10.0 / sqrt($sq_x + $sq_z + 0.2);
			}
		}
	}

	protected static function setBiomeSpecificGround(GroundGenerator $gen, int ...$biomes) : void{
		foreach($biomes as $biome){
			self::$GROUND_MAP[$biome] = $gen;
		}
	}

	protected const COORDINATE_SCALE = 684.412;
	protected const HEIGHT_SCALE = 684.412;
	protected const HEIGHT_NOISE_SCALE_X = 200.0;
	protected const HEIGHT_NOISE_SCALE_Z = 200.0;
	protected const DETAIL_NOISE_SCALE_X = 80.0;
	protected const DETAIL_NOISE_SCALE_Y = 160.0;
	protected const DETAIL_NOISE_SCALE_Z = 80.0;
	protected const SURFACE_SCALE = 0.0625;
	protected const BASE_SIZE = 8.5;
	protected const STRETCH_Y = 12.0;
	protected const BIOME_HEIGHT_OFFSET = 0.0;
	protected const BIOME_HEIGHT_WEIGHT = 1.0;
	protected const BIOME_SCALE_OFFSET = 0.0;
	protected const BIOME_SCALE_WEIGHT = 1.0;
	protected const DENSITY_FILL_MODE = 0;
	protected const DENSITY_FILL_SEA_MODE = 0;
	protected const DENSITY_FILL_OFFSET = 0.0;

	private GroundGenerator $ground_gen;
	private string $type = WorldType::NORMAL;

	public function __construct(int $seed, string $preset_string){
		$preset = SimpleGeneratorPreset::parse($preset_string);
		parent::__construct(
			$seed,
			$preset->exists("environment") ? Environment::fromString($preset->getString("environment")) : Environment::OVERWORLD,
			$preset->exists("worldtype") ? WorldType::fromString($preset->getString("worldtype")) : null,
			$preset
		);
		$this->ground_gen = new GroundGenerator();
		$this->addPopulators(new OverworldPopulator(), new SnowPopulator());
	}

	public function getGroundGenerator() : GroundGenerator{
		return $this->ground_gen;
	}

	protected function generateChunkData(ChunkManager $world, int $chunk_x, int $chunk_z, VanillaBiomeGrid $grid) : void{
		$this->generateRawTerrain($world, $chunk_x, $chunk_z);

		$cx = $chunk_x << 4;
		$cz = $chunk_z << 4;

		$octave_generator = $this->getWorldOctaves()->surface;
		$size_x = $octave_generator->getSizeX();
		$size_z = $octave_generator->getSizeZ();

		$surface_noise = $octave_generator->getFractalBrownianMotion($cx, 0.0, $cz, 0.5, 0.5);

		$chunk = $world->getChunk($chunk_x, $chunk_z);

		for($x = 0; $x < $size_x; ++$x){
			for($z = 0; $z < $size_z; ++$z){
				$chunk->setBiomeId($x, $z, $id = $grid->getBiome($x, $z));
				if($id !== null && array_key_exists($id, self::$GROUND_MAP)){
					self::$GROUND_MAP[$id]->generateTerrainColumn($world, $this->random, $cx + $x, $cz + $z, $id, $surface_noise[$x | $z << 4]);
				}else{
					$this->ground_gen->generateTerrainColumn($world, $this->random, $cx + $x, $cz + $z, $id, $surface_noise[$x | $z << 4]);
				}
			}
		}
	}

	protected function createWorldOctaves() : WorldOctaves{
		$seed = new Random($this->random->getSeed());

		$height = PerlinOctaveGenerator::fromRandomAndOctaves($seed, 16, 5, 1, 5);
		$height->setXScale(self::HEIGHT_NOISE_SCALE_X);
		$height->setZScale(self::HEIGHT_NOISE_SCALE_Z);

		$roughness = PerlinOctaveGenerator::fromRandomAndOctaves($seed, 16, 5, 33, 5);
		$roughness->setXScale(self::COORDINATE_SCALE);
		$roughness->setYScale(self::HEIGHT_SCALE);
		$roughness->setZScale(self::COORDINATE_SCALE);

		$roughness2 = PerlinOctaveGenerator::fromRandomAndOctaves($seed, 16, 5, 33, 5);
		$roughness2->setXScale(self::COORDINATE_SCALE);
		$roughness2->setYScale(self::HEIGHT_SCALE);
		$roughness2->setZScale(self::COORDINATE_SCALE);

		$detail = PerlinOctaveGenerator::fromRandomAndOctaves($seed, 8, 5, 33, 5);
		$detail->setXScale(self::COORDINATE_SCALE / self::DETAIL_NOISE_SCALE_X);
		$detail->setYScale(self::HEIGHT_SCALE / self::DETAIL_NOISE_SCALE_Y);
		$detail->setZScale(self::COORDINATE_SCALE / self::DETAIL_NOISE_SCALE_Z);

		$surface = SimplexOctaveGenerator::fromRandomAndOctaves($seed, 4, 16, 1, 16);
		$surface->setScale(self::SURFACE_SCALE);

		return new WorldOctaves($height, $roughness, $roughness2, $detail, $surface);
	}

	protected function generateRawTerrain(ChunkManager $world, int $chunk_x, int $chunk_z) : void{
		$density = $this->generateTerrainDensity($chunk_x, $chunk_z);

		$sea_level = 64;

		$fill = self::DENSITY_FILL_MODE;
		$afill = abs($fill);
		$sea_fill = self::DENSITY_FILL_SEA_MODE;
		$density_offset = self::DENSITY_FILL_OFFSET;

		$still_water = VanillaBlocks::WATER()->getStillForm()->getFullId();
		$water = VanillaBlocks::WATER()->getFlowingForm()->getFullId();
		$stone = VanillaBlocks::STONE()->getFullId();

		$chunk = $world->getChunk($chunk_x, $chunk_z);

		for($i = 0; $i < 5 - 1; ++$i){
			for($j = 0; $j < 5 - 1; ++$j){
				for($k = 0; $k < 33 - 1; ++$k){
					$d1 = $density[self::densityHash($i, $j, $k)];
					$d2 = $density[self::densityHash($i + 1, $j, $k)];
					$d3 = $density[self::densityHash($i, $j + 1, $k)];
					$d4 = $density[self::densityHash($i + 1, $j + 1, $k)];

					$d5 = ($density[self::densityHash($i, $j, $k + 1)] - $d1) / 8;
					$d6 = ($density[self::densityHash($i + 1, $j, $k + 1)] - $d2) / 8;
					$d7 = ($density[self::densityHash($i, $j + 1, $k + 1)] - $d3) / 8;
					$d8 = ($density[self::densityHash($i + 1, $j + 1, $k + 1)] - $d4) / 8;
					for($l = 0; $l < 8; ++$l){
						$d9 = $d1;
						$d10 = $d3;

						$y_pos = $l + ($k << 3);
						$y_block_pos = $y_pos & 0xf;
						$sub_chunk = $chunk->getSubChunk($y_pos >> 4);

						for($m = 0; $m < 4; ++$m){
							$dens = $d9;
							for($n = 0; $n < 4; ++$n){
								if($afill === 1 || $afill === 10 || $afill === 13 || $afill === 16){
									$sub_chunk->setFullBlock($m + ($i << 2), $y_block_pos, $n + ($j << 2), $water);
								}elseif($afill === 2 || $afill === 9 || $afill === 12 || $afill === 15){
									$sub_chunk->setFullBlock($m + ($i << 2), $y_block_pos, $n + ($j << 2), $stone);
								}

								if(($dens > $density_offset && $fill > -1) || ($dens <= $density_offset && $fill < 0)){
									if($afill === 0 || $afill === 3 || $afill === 6 || $afill === 9 || $afill === 12){
										$sub_chunk->setFullBlock($m + ($i << 2), $y_block_pos, $n + ($j << 2), $stone);
									}elseif($afill === 2 || $afill === 7 || $afill === 10 || $afill === 16){
										$sub_chunk->setFullBlock($m + ($i << 2), $y_block_pos, $n + ($j << 2), $still_water);
									}
								}elseif(($y_pos < $sea_level - 1 && $sea_fill === 0) || ($y_pos >= $sea_level - 1 && $sea_fill === 1)){
									if($afill === 0 || $afill === 3 || $afill === 7 || $afill === 10 || $afill === 13){
										$sub_chunk->setFullBlock($m + ($i << 2), $y_block_pos, $n + ($j << 2), $still_water);
									}elseif($afill === 1 || $afill === 6 || $afill === 9 || $afill === 15){
										$sub_chunk->setFullBlock($m + ($i << 2), $y_block_pos, $n + ($j << 2), $stone);
									}
								}

								$dens += ($d10 - $d9) / 4;
							}

							$d9 += ($d2 - $d1) / 4;

							$d10 += ($d4 - $d3) / 4;
						}

						$d1 += $d5;
						$d3 += $d7;
						$d2 += $d6;
						$d4 += $d8;
					}
				}
			}
		}
	}

	protected function generateTerrainDensity(int $x, int $z) : array{
		$density = [];

		$x <<= 2;
		$z <<= 2;

		$biomeGrid = $this->getBiomeGridAtLowerRes($x - 2, $z - 2, 10, 10);

		$octaves = $this->getWorldOctaves();
		$height_noise = $octaves->height->getFractalBrownianMotion($x, 0, $z, 0.5, 2.0);
		$roughness_noise = $octaves->roughness->getFractalBrownianMotion($x, 0, $z, 0.5, 2.0);
		$roughness_noise_2 = $octaves->roughness_2->getFractalBrownianMotion($x, 0, $z, 0.5, 2.0);
		$detail_noise = $octaves->detail->getFractalBrownianMotion($x, 0, $z, 0.5, 2.0);

		$index = 0;
		$index_height = 0;

		for($i = 0; $i < 5; ++$i){
			for($j = 0; $j < 5; ++$j){
				$avg_height_scale = 0.0;
				$avg_height_base = 0.0;
				$total_weight = 0.0;
				$biome = $biomeGrid[$i + 2 + ($j + 2) * 10];
				$biome_height = BiomeHeightManager::get($biome);
				for($m = 0; $m < 5; ++$m){
					for($n = 0; $n < 5; ++$n){
						$near_biome = $biomeGrid[$i + $m + ($j + $n) * 10];
						$near_biome_height = BiomeHeightManager::get($near_biome);
						$height_base = self::BIOME_HEIGHT_OFFSET + $near_biome_height->getHeight() * self::BIOME_HEIGHT_WEIGHT;
						$height_scale = self::BIOME_SCALE_OFFSET + $near_biome_height->getScale() * self::BIOME_SCALE_WEIGHT;
						if($this->type === WorldType::AMPLIFIED && $height_base > 0){
							$height_base = 1.0 + $height_base * 2.0;
							$height_scale = 1.0 + $height_scale * 4.0;
						}

						$weight = self::$ELEVATION_WEIGHT[self::elevationWeightHash($m, $n)] / ($height_base + 2.0);
						if($near_biome_height->getHeight() > $biome_height->getHeight()){
							$weight *= 0.5;
						}

						$avg_height_scale += $height_scale * $weight;
						$avg_height_base += $height_base * $weight;
						$total_weight += $weight;
					}
				}
				$avg_height_scale /= $total_weight;
				$avg_height_base /= $total_weight;
				$avg_height_scale = $avg_height_scale * 0.9 + 0.1;
				$avg_height_base = ($avg_height_base * 4.0 - 1.0) / 8.0;

				$noise_h = $height_noise[$index_height++] / 8000.0;
				if($noise_h < 0){
					$noise_h = -$noise_h * 0.3;
				}

				$noise_h = $noise_h * 3.0 - 2.0;
				if($noise_h < 0){
					$noise_h = max($noise_h * 0.5, -1) / 1.4 * 0.5;
				}else{
					$noise_h = min($noise_h, 1) / 8.0;
				}

				$noise_h = ($noise_h * 0.2 + $avg_height_base) * self::BASE_SIZE / 8.0 * 4.0 + self::BASE_SIZE;
				for($k = 0; $k < 33; ++$k){
					$nh = ($k - $noise_h) * self::STRETCH_Y * 128.0 / 256.0 / $avg_height_scale;
					if($nh < 0.0){
						$nh *= 4.0;
					}

					$noise_r = $roughness_noise[$index] / 512.0;
					$noise_r_2 = $roughness_noise_2[$index] / 512.0;
					$noise_d = ($detail_noise[$index] / 10.0 + 1.0) / 2.0;

					$dens = $noise_d < 0 ? $noise_r : ($noise_d > 1 ? $noise_r_2 : $noise_r + ($noise_r_2 - $noise_r) * $noise_d);
					$dens -= $nh;
					++$index;
					if($k > 29){
						$lowering = ($k - 29) / 3.0;
						// linear interpolation
						$dens = $dens * (1.0 - $lowering) + -10.0 * $lowering;
					}
					$density[self::densityHash($i, $j, $k)] = $dens;
				}
			}
		}
		 return $density;
	}
}

OverworldGenerator::init();
