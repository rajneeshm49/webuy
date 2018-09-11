1) webuy.zip consists of cakephp API's and client.zip consists of front end in angular

2) The project is Authentication based application comprising of Customer frontend and admin backend.

3) The appication uses JWT authentication method to verify the user. Every hit to server has JWT token in header (except few such as Login, Logout) that we get after user/admin login 

3) Technologies used
	a) Angular 6.1
	b) Cakephp 3.6
	c) MySQL
	d) JWT for authentication
	e) Angular material for designing 

4) The base url for server API's is stored in environment.ts in client frontend(src/environment.ts) which in this case is 'http://localhost'




STEPS TO START PROJECT

prerequisite: Node and WAMP/XAMPP needs to be installed

1 unzip client.zip into client folder and webuy.zip into webuy folder and place webuy folder in wamp folder
2 go to webuy/config/app.php and chnage the db configurations(probably on line no. 260)
2 'cd client' (coming to angular part)
3 'ng serve' - the angular will start running and will show the port as well on which it is running
4 go to the url. default is 'http://localhost:4200' 



CREDENTIALS

User Section
url - http://localhost:4200
email: rajneesh.m49@gmail.com
password: 123456789

Admin Backend section
url - http://localhost:4200/admin
username: webuy@gmail.com
password: webuy123$

Note: customers and admin users are stored into same table users with only difference - admin users have column role='admin'
