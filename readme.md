# Syngenta Tymirium

**Software In Use**

- [Laravel](https://laravel.com)
- [Laravel Nova](https://nova.laravel.com)

**Local Setup**

It is recommended to use [Laravel Homestead](https://laravel.com/docs/8.x/homestead) for your development environment. 
If you cannot use Homestead, or have another local development setup, then ensure that your local environment is using 
the same minimum requirements for Laravel. See the [documentation](https://laravel.com/docs/8.x/installation).

You will also need to have NodeJS and NPM installed locally. 

You will need to use the `develop` branch as your starting branch and branch from there.

Once you have pulled the `develop` branch down to your local machine, you can log in to the staging server and do a 
`mysqldump` of the `cleo_syngenta_dev_theformery_com` database, so you have a local copy of the DB. The password for 
the database is in the `.env` file in `/var/www/cleo.syngenta.dev.theformery.com/htdocs`

While you are logged in to the server, make a copy of the `.env` file for your local install and replace with your local
environment DB credentials.

You will need to create a Laravel Nova admin user `php artisan nova:user` - the 
[docs](https://nova.laravel.com/docs/3.0/installation.html) have more information if you need.

Finally, to ensure you can login to the admin panel `yourtestdomain.dev/admin` you will need to add your email address 
you used to create your admin user to the `app/Providers/NovaServiceProvider.php` `gate()` method.

**Frontend and Build Updates**

To run your frontend changes, or if you made any changes to the packages, you can follow the various steps in the `devops/deploys/staging.sh` script.

`composer install` will run laravel package installations

`npm run watch` or `npm run dev` will package frontend changes to JS/CSS

`npm install` will install all required frontend packages.

**Deploys**

To deploy to staging, follow the same process as we use on other projects. Once you have completed and tested your work 
locally and are ready to test on staging, create a Pull Request from your branch into `develop` and once you are happy, 
approve your PR and merge it in to `develop`. You can then open a new Pull Request from `develop` into `deploy/staging` 
and that will automatically trigger a deploy.

You can [monitor your deploy pipeline here](https://bitbucket.org/theformery/cleo-manuals/addon/pipelines/home) - 
estimated deploy timeframe is about 4-5minutes.

