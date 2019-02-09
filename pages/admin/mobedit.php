<?php
$mobs = R::findAll(DBTables::MOB," order by id desc");

//$mob1 = json_encode($mobs[1]);
//
//$uM = json_decode($mob1,TRUE);
//$uM["name"].="1";
//
//$g = R::dispense(DBTables::MOB);
//$g->import($uM);
//R::store($g);
if (isset($_POST['name'])) {

    if (!isset($_SESSION["saveid"]) || $_SESSION["saveid"] != $_GET["save"]) {
        $g = R::dispense(DBTables::MOB);

        if (isset($_POST["rare"])) {
            $_POST["rare"] = true;
        } else {
            $_POST["rare"] = false;
        }

        if (isset($_POST["questmob"])) {
            $_POST["questmob"] = true;
        } else {
            $_POST["questmob"] = false;
        }

        echo "Mob gespeichert. ID: " . $_POST["id"];
        $g->import($_POST);
        R::store($g);
        $_SESSION["saveid"] = $_GET["save"];
    }
}

if (!isset($_GET['a'])) {
    ?>
    <div id="mobselect">

        <table>
            <tr>
                <th></th> <th>Id</th><th>Name</th><th>level</th><th>mobkey</th><th>ki</th><th>Stats</th>
            </tr>
            <?php
            $fields = ["id", "name", "level", "mobkey", "ki"];

            foreach ($mobs as $m) {
                echo "<tr>";
                echo "<td><a class='edit' href='?page=mobedit&a=edit&id=$m->id'>edit</a><br/><a href='?page=mobedit&a=copy&id=$m->id' class='copy' data-id='$m->id'>copy</a></td>";
                foreach ($fields as $f) {
                    echo "<td>" . $m->$f . "</td>";
                }
                echo "<td><small>" . butyfyStats($m->fightstats) . "</small></td>";

                echo "</tr>";
            }
            ?>

        </table>
        <?php
    } if (isset($_GET['a'])) {

        $eMob = $mobs[$_GET['id']];
        $action = $_GET["a"];

        $config = [
            'id' => 'none',
            'name' => 'text',
            'type' => 'optiontype',
            'fightstats' => 'stats',
            'level' => 'text',
            'rare' => 'optionrare',
            'questmob' => 'checkbox',
            'mobkey' => 'text',
            'loot' => 'loot',
            'actions' => 'action',
            'ki' => 'text',
            'attacklist' => 'attacklist'
        ];
        ?>



        <div id="mobedit">       <a href='?page=mobedit'>Zurück</a>
            <form action="?page=mobedit&save=<?php echo time() . "s" . rand(1, 12313) ?>" method="post">
                <input type="hidden" name="id" value="<?php if ($_GET['a'] == "edit") echo $eMob->id; ?>"/>
                <table>

                    <?php ?>

                    <tr>
                        <td>Name </td>
                        <td><input type="text" name="name" value="<?php echo $eMob->name; ?>"/></td>
                    </tr>
                    <tr>   
                        <td>Mob Typ </td>
                        <td>
                            <select name="type" value="<?php echo $eMob->type; ?>">
                                <option value="1">Humanoid</option>
                                <option value="0">Tier</option>
                                <option value="2">Geist</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Mob Key</td>
                        <td><input type="text" name="mobkey" value="<?php echo $eMob->mobkey; ?>"/></td>

                    </tr>
                    <tr>
                        <td>Ki</td>
                        <td><input type="text" name="ki" value="<?php echo $eMob->ki; ?>"/></td>
                    </tr>
                    <tr>
                        <td>Level</td>
                        <td><input type="text" name="level" value="<?php echo $eMob->level; ?>"/></td>
                    </tr>
                    <tr>
                        <td>Rar-Mob</td>
                        <td><input type="checkbox" <?php if ($eMob->rare == 1) echo "checked"; ?> name="rare" /></td>
                    </tr>
                    <tr>
                        <td>Questmob</td>
                        <td><input type="checkbox" <?php if ($eMob->questmob == 1) echo "checked"; ?> name="questmob" /></td>
                    </tr>
                    <tr>
                        <td>Loot</td>
                        <td>
                            <input type="hidden" value='<?php echo $eMob->loot; ?>' name='loot' />
                            Xp: Min: <input type="text" size="1" value="" class='loot_xp_mi'/> Max: <input type="text" size="1" value="" class='loot_xp_ma'/> W: <input type="text" value="" class='loot_xp_w' size="1"/><br/>
                            Gold: <input type="text" size="1" value="" class='loot_gold_mi'/> Max: <input type="text" size="1" value="" class='loot_gold_ma'/> W: <input type="text" value="" class='loot_gold_w' size="1"/> <br/>
                            Ehre:<input type="text" size="1" value="" class='loot_honor_mi'/> Max: <input type="text" size="1" value="" class='loot_honor_ma'/> W: <input type="text" value="" class='loot_honor_w' size="1"/> <br/>
                            Itemvalue: {{id}},{{anzahl}}
                            <br/>
                            Item:<input type="text" size="1" value="" class='loot_item1'/> W: <input type="text" value="" class='loot_item1_w' size="1"/> <br/>
                            Item2:<input type="text" size="1" value="" class='loot_item2'/> W: <input type="text" value="" class='loot_item2_w' size="1"/> <br/>
                            Item3:<input type="text" size="1" value="" class='loot_item3'/> W: <input type="text" value="" class='loot_item3_w' size="1"/> <br/>
                            Item4:<input type="text" size="1" value="" class='loot_item4'/> W: <input type="text" value="" class='loot_item4_w' size="1"/> <br/>

                        </td>
                    </tr>
                    <tr>
                        <td>Stats: </td>
                        <td>
                            <input type='hidden' name='fightstats' value='<?php echo $eMob->fightstats; ?>'/>
                            armoreRed <input size="2" value="1" class="m" data-name="armoreRed"/><br/>
                            avoid <input size="2" value="1"  class="m" data-name='avoid'/><br/>
                            crit <input size="2" value="1"  class="m" data-name="crit"/><br/>
                            minDmg <input size="2" value="1"  class="m" data-name='minDmg'/><br/>
                            maxDmg <input size="2" value="2"  class="m" data-name="maxDmg"/><br/>
                            maxHp <input size="2" value="10"  class="m" data-name='maxHp'/><br/>
                            maxMana <input size="2" value="1"  class="m" data-name='maxMana'/><br/>
                            speed <input size="2" value="1"  class="m" data-name='speed'/><br/>
                            critMulti <input size="2" value="1"  class="m" data-name='critMulti'/><br/>
                            aggro <input size="2" value="1"  class="m" data-name='spell'/><br/>
                            spell <input size="2" value="1"  class="m" data-name='spell'/><br/>
                            manaReg <input size="2" value="1"  class="m" data-name='manaReg'/><br/>
                        </td>
                    </tr>
                    <tr>
                        <td>Kill Action</td>
                        <td>
                            <input type='hidden' name='actions' value='<?php echo $eMob->actions; ?>'/>
                            <div id='builder'>

                            </div>
                            <div class='addAction'>Add</div>
                        </td>
                    </tr>
                    <tr>
                        <td>Attacks</td>
                        <td>
                            <input type='text' name='attacklist' value='<?php echo $eMob->attacklist; ?>' />

                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit"  value="Speichern" /></td>
                    </tr>
                </table>
            </form>
           
        </div>



    </div>

    <div id='lookup'>
                <div><input type='text' data-name='search' /><select class='sm'>
                        <option>mob</option>
                        <option>item</option>
                        <option>equip</option>
                        <option>attack</option>
                        <option>quest</option>              
            </select><button class="s">S</button></div>
        <div class='lookup_output'>
            asdfasd
        </div>
    </div> 
<script>
                $("select[name='type'").val(<?php echo $eMob->type ?>);

                lootRender();
                    lootBuilder();
                    statsRender();
                    actionBuilder();
                    showItemLookup();
                                    </script>
<?php } ?>