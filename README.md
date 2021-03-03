## Booking Engine
Booking Engine with Symfony Framework.

# Server Set up :

### First create the database
	
``` bash
	 symfony console doctrine:database:create
```

``` bash
	symfony console doctrine:migration:migrate
```

  * consider using the current migrations !


### Seconde Create an admin for the booking engine	


``` bash
	symfony console doctrine:query:sql "INSERT INTO `admin`(`hotel_id`, `roles`, `password`, `email`, `tele`, `cin_or_passport`) VALUES (null,'[\"ROLE_SUPER_ADMIN\"]','$2y$13$WYwGwbb5sIKVcA8QEvP8yO5fb29QN1S.6h2FyVdQf0TOdMx1lpaoK','root@alm.ma',2120000000,'XX0000')"
```

 ###	Admin credentials 
 
  * email: root@alm.ma
  * password : root

  * please consider changing your credentials later !!


### And last start your server

``` bash
	symfony serve
```
