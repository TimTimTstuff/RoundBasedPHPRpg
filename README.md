# RoundBasedPHPRpg
A "can i make a rpg in PHP whit round based Fights" .. i can

# Install
1. Import the sql
2. Modify the configuration.php with your connection data
3. Login: tino pw: test


# Users
To add new Users, add a new record in the user table. 
just name and password. The password is in plain text. 


# Important
This is just a PoC, don't run this in public, just for your on fun in a private network. 


# Special Attacks / Classes
There is no UI to learn attacks or choos a class. You can change thet in the charackter table. 

The possible Values are in: lib/database/databaseconnect.php
    const CLASSNAME = [
        0=>"Miliz",
        1=>"Schutz Krieger",
        2=>"Berserker",
        3=>"Zauberer",
        4=>"Priester",
        5=>"Bogenschütze",
        6=>"Assasine"
    ];

Classes: 1: Tank, 2: Off-Warrior, 3: Mage, 4: Priest, 5: Hunter, 6: Assasine

If you want to add attacks (spells) to a charackter, you have modify the attacklist field at the charackter. 
It is a array, you can add attacks by putting the id in the [1,2,3]
Attacks a found in the fightattacks table. 



