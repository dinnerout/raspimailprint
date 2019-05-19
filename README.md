# raspimailprint

An ready to use script for builing an E-Mail printer with an Raspberry Pi and a Thermal printer. See full discription here: <a href="http://blog.sebastian-martens.de/technology/how-to-build-your-own-e-mail-printer/">http://blog.sebastian-martens.de/technology/how-to-build-your-own-e-mail-printer/</a>

## Install

First be sure you are in the default pi- user home directory

		cd ~

Lets update the current software to the lastest version:

		sudo apt-get update

Because I used an old mail script written in PHP we need PHP and the IMAP extension as well:

		sudo apt-get install php5
		sudo apt-get install php5-imap

All the code is available via github. So just checkout the code by using:
		
		git clone git://github.com/dinnerout/raspimailprint.git

Switch in into the code directory:
		
		cd raspimailprint

Now we need to make a the mail-script executable:
		
		chmod +x fetchMails.sh

The last step is to configure your E-Mail access data. Do this in the config/config.php file.
		
		cp config/example_config.php config/config.php
		nano config/config.php

You need to update the lines EMAIL_SERVER, EMAIL_USER, EMAIL_PASSWORD. Make sure you checked the other settings. By default downloaded mails will be deleted from the server.

## Usage

Now you are ready to execute the mail script and print your mails.
		
		./fetchMails.sh
		
## License
Copyright © 2016 Sebastian Martens

Released under the MIT license.