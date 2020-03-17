<?php

namespace Limit;

use pocketmine\block\Block;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {

    public function onEnable()
    {
        @mkdir($this->getDataFolder());
		$this->saveResource("Config.yml");
		$cfg = new Config($this->getDataFolder()."Config.yml", Config::YAML);
        $this->commands = $cfg->get("Commands");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
  }

    public function onInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $blocks = $event->getBlock()->getId();
        $blacklist = [Block::ENDER_CHEST, Block::CRAFTING_TABLE, Block::CHEST, Block::FURNACE, Block::BURNING_FURNACE, Block::TRAPPED_CHEST, Block::ENCHANTMENT_TABLE, Block::ANVIL, Block::ITEM_FRAME_BLOCK, Block::SHULKER_BOX, Block::TNT, Block::DROPPER, Block::DISPENSER, Block::UNDYED_SHULKER_BOX, Block::MONSTER_SPAWNER, Block::HOPPER_BLOCK];
        if ($player->isCreative()){
            if (in_array($blocks, $blacklist)){
                $event->setCancelled();
                $player->sendTip("§eYou can't interact with this item in creative mode!");
          if ($player->hasPermission("lgm.bypass.touch")){
          	$event->setCancelled(false);
                return;
            }
         }
      }
    }
 
    public function onPlace(BlockPlaceEvent $event) {
        $player = $event->getPlayer();
        $blocks = $event->getBlock()->getId();
        $blacklist = [Block::ENCHANTMENT_TABLE, Block::ANVIL, Block::ITEM_FRAME_BLOCK, Block::SHULKER_BOX, Block::TNT, Block::DIAMOND_BLOCK, Block::IRON_BLOCK, Block::LAPIS_BLOCK, Block::EMERALD_BLOCK, Block::COAL_BLOCK, Block::UNDYED_SHULKER_BOX, Block::DIAMOND_ORE, Block::QUARTZ_ORE, Block::EMERALD_ORE, Block::COAL_ORE, Block::REDSTONE_ORE, Block::LAPIS_ORE, Block::IRON_ORE, Block::GOLD_ORE, Block::GOLD_BLOCK, Block::OBSIDIAN, Block::BEDROCK, Block::END_PORTAL_FRAME, Block::PISTON, Block::STICKY_PISTON, Block::BEACON, Block::MONSTER_SPAWNER, Block::HOPPER_BLOCK];
        if ($player->isCreative()){
        	if(in_array($blocks, $blacklist)){
        	$event->setCancelled();
            $player->sendTip("§eYou can't place this block in creative mode");
          if ($player->hasPermission("lgm.bypass.place")){
          	$event->setCancelled(false);
            return;
         }
       }
     }
   }

    public function onGameModeChange(PlayerGameModeChangeEvent $event) {
        $player = $event->getPlayer();
        $newGM = $event->getNewGamemode();
        if ($newGM === 0 && $newGM === 2){
            $player->getInventory()->clearAll(true);
            $player->getArmorInventory()->clearAll(true);
            $player->sendTip("§aYour inventory cleared");
            if ($player->hasPermission("lgm.bypass.gamemode")){
          	$player->getInventory()->clearAll(false);
               $player->getArmorInventory()->clearAll(false);
            return;
        }
    }
 }

    public function onDropItem(PlayerDropItemEvent $event)
    {
        $player = $event->getPlayer();
        if ($player->isCreative()){
            $event->setCancelled();
            $player->sendTip("§cYou can't drop item in creative mode!");
         if ($player->hasPermission("lgm.bypass.drop")){
          	$event->setCancelled(false);
        }
      }
    }

    public function onAttack(EntityDamageEvent $event){
        if ($event instanceof EntityDamageByEntityEvent){
            $player = $event->getDamager();
            if ($player instanceof Player){
                if ($player->isCreative()) {
                    $event->setCancelled();
                    $player->sendTip("§eYou can't hit player using creative mode");
                   if ($player->hasPermission("lgm.bypass.hit")){
          	        $event->setCancelled(false);
                }
              }
           }
        }
     }
     public function onCmd(PlayerCommandPreprocessEvent $ev) {
          $player = $ev->getPlayer();
           if ($player->isCreative()) {           	
             $cmd = explode(" ", strtolower($ev->getMessage()));
              foreach($this->commands as $cmdname){
               if($cmd[0] === $cmdname){
                $ev->setCancelled(true);
                 $player->sendMessage("§cThat command can't use in gamemode creative!");
         }
      }
   }
 }

}
