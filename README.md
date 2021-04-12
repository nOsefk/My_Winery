# My Winery
## Projet Web - Bac Info 3

A Basic Website selling Wine using MVC.

## Technologies 

![](https://img.shields.io/badge/Symfony-4.23.5-green?logo=symfony)
![](https://img.shields.io/badge/PHP-7.4.11-777BB4?logo=php)
![](https://img.shields.io/badge/MySQL-5.7.23-4479A1?logo=mysql)
![](https://img.shields.io/badge/Bootstrap-4.6-7952B3?logo=bootstrap)
![](https://img.shields.io/badge/HMTL-5-E34F26?logo=html5)
![](https://img.shields.io/badge/CSS-3-1572B6?logo=css3)


## Users

### Register

As a visitor, on the navbar you can in any page register or log in.

![home](/screenshots/01-users.png)

On the Register page you have to fill the TypeForm for registration, succeed a captcha and validate the [Terms and Conditions](https://www.termsfeed.com/live/85b82686-19e7-4c5b-b694-ce3476f75477)

![register](/screenshots/02-register.png)

If evertyhing is okay you receive a confirmation email simulated with [Test Mail Server Tool](https://toolheap.com/test-mail-server-tool/)

Login is a simple log in page :

![login](/screenshots/03-login.png)

And once you're logged in you can see your name + cart and a possibility to log out.

![logged](/screenshots/04-logged.png)


### Admin & Rights

There are 3 different roles with different rights on the website.

For example, on a product, a visitor will be redirect to the log in when adding products to his cart :

![visitor](/screenshots/05-visitor.png)

The button add the product to the cart for a user :

![user](/screenshots/06-user.png)

Admins will be able to edit the product :

![admin](/screenshots/07-admin.png)

Super admin will be able to delete the product.

![super](/screenshots/08-super.png)

These rights apply all over the website... 

![super2](/screenshots/09-super2.png)

Editing will open a regular form :

![super3](/screenshots/10-super3.png)

Creating a new product requires the upload of an image :

![upload](/screenshots/18-ulpoad.png)

### Commenting

Every product has its own page and own comments :

![com2](/screenshots/12-com2.png)

If the user poster the comment he can edit or delete it :

![com1](/screenshots/11-com1.png)


## Content

### Homepage

Website has a Homepage with the 2 last updated products :

![homepage](/screenshots/13-homepage.png)


### Products

A list of products with pagination :

![product](/screenshots/14-product.png)

### Categories & Region

That can be sorted by category or region :

![regions](/screenshots/15-regions.png)
![category](/screenshots/16-category.png)

### Contact 

A contact page with form :

![category](/screenshots/17-contact.png)

## Cart

We have alsa a cart management :

- Possibility to add or remove product from the cart with quantity management.
- Total with and without taxes.
- Button to empty the cart or to buy it after accepting the [Terms and Conditions](https://www.termsfeed.com/live/85b82686-19e7-4c5b-b694-ce3476f75477).

![cart](/screenshots/19-cart.png)