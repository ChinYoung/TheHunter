Hello,

Thanks for purchasing TheHunter, the easiest way to collect email addresses from websites!

This version of TheHunter contains the following options.

- Validate email addresses
- Save only unique email addresses
- Block email domains or by using wildcard
- Export collected email addresses to text or excel compatiable file
- Maximum number of urls to check
- Maximum number of url loads per round
- Easy to setup cronjob

To install;

- Copy all files to a directory on your website/webserver
- Change rights of the following directories to atleast 0755 (linux based operating systems)
  ./cache
  ./system
  ./logs
- Create a database for this script using phpmyadmin or another database editor
- Insert database structure by importing structure.sql
- Edit system/config.ini
  change all information possible: eg; timezone, database, email, key for cronjob and such. 
  Please follow the example settings as guide.

- Create the cronjob using this example;
0,30	*	*	*	*	 /usr/local/bin/php -q /home/admin/domains/**yourdomainname*/public_html/cronjob.php *** YOUR KEY IN config.INI*** >>/home/admin/domains/**yourdomainname*/public_html/logs/cronjoblog-`date +%d%m%y`.log

Explanation: start cronjob on full and half hours.

Then after all this, load the script into your browser and you'll see a registration form to fill in; write username and password and click on Install. After this is done you'll see the index, the entered login information will be ready for you to use anytime.

If you would like to change the password, you need to empty the users table (by hand) with the following query on phpmyadmin

DELETE FROM `users`

After this, start the script again and you'll see the installation screen again, enter the information and all is ready for you.

If you have any questions, problems or suggestions, please send a email to droidtweak@gmail.com. If its about a script error please describe in full what the problem is and if possible copy the errors.

If you need help installing this script, we can help you for a small price, contact us.

And give us a thumb up on facebook! https://www.facebook.com/EmailBulkRipper

Have fun.
https://NocRoom.com
