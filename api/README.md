Haulin Aggs Api
========================
About
Haulin Agg is requesting proposals from various app and web development companies to build our new mobile platform app designed to simplify the lives of construction industry companies, specifically those involved in the sale, transport, and purchase/use of aggregate products like sand and gravel.

Requirements
------------

  * PHP 8.2.0 or higher;
  * Mysql PHP extension enabled;
  * and the [usual Symfony application requirements][2].

Installation
------------

### Follow the steps to setup the project locally.

1. <img src="https://img.icons8.com/ios/20/000000/code-fork.png"/> **Fork** the **`api-haulin-aggs`** repository into your account.
2. <img src="https://img.icons8.com/external-flat-icons-inmotus-design/20/000000/external-clone-clone-flat-icons-inmotus-design-2.png"/> **Clone** the **`api-haulin-aggs`** repository.

   ```
   git clone git@github.com:shikhartech/api-haulin-aggs.git
   ```

   OR

   ```
   git clone https://github.com/shikhartech/api-haulin-aggs.git
   ```

   > While cloning, you maybe asked to submit `Personal Access Token` which you can generate from `Settings > Developer Settings`.

3. **Change** into the project folder.

   ```
   cd <project_name>
   ```

   Example:

   ```
   cd api-haulin-aggs
   ```

4. **Run** the `Composer Install` command.

   ```
   composer install
   ```

   > The `Composer Install` command installs all the project dependencies.

5. **Duplicate** `.env.example` and **Rename** to `.env` using the following command.

   ```
   cp .env.example .env
   ```

   > If `.env.example` does not exist, create a new `.env` file.

6. **Open** `.env` and **Enter** your details in the required fields.

   - **DATABASE_URL** ="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf"
   - **APP_LIVE** = false
   - **GOOGLE_MAP_API_KEY** = <your_api_key>
   - **MAILER_DSN** = <your_mailer_dns>
   - **APP_URL** = <app_url>
   - **FRONTEND_URL** = <super_admin_portal_url>
   - **PLAY_STORE_LINK** = <android_app_url>
   - **APP_STORE_LINK** = <iso_app_url>
   - **PUSHER_BEAMS_INSTANCE_ID** = <pusher_beams_instance_id>
   - **PUSHER_BEAMS_SECRET_KEY** = <pusher_beams_secret_key>

7. **update** an empty database. Run the following command.

   ```
   php bin/console doctrine:schema:update -f --complete
   ```

   > The `php bin/console doctrine:schema:update -f --complete` command update schema of the databases.

8. **JWT_CONFIGURATION** in app. Run the following command.

   ```
   php bin/console lexik:jwt:generate-keypair
   ```

   > The `lexik:jwt:generate-keypair` command generate private and public key in app.
 <br>

**<i>Congratulations!</i>** You have successfully setup the project into your machine. Now, run `symfony server:start` to start the project.

```
symfony server:start
```


Usage
-----

There's no need to configure anything before running the application. There are
2 different ways of running this application depending on your needs:

**Option 1.** [Download Symfony CLI][4] and run this command:

```bash
$ cd my_project/
$ symfony server:start
```

Then access the application in your browser at the given URL (<https://localhost:8000> by default).

**Option 2.** Use a web server like Nginx or Apache to run the application
(read the documentation about [configuring a web server for Symfony][3]).

On your local machine, you can run this command to use the built-in PHP web server:

```bash
$ cd my_project/
$ php -S localhost:8000 -t public/
```

Tests
-----

Execute this command to run tests:

```bash
$ cd my_project/
$ ./bin/phpunit
```

Linter
-----

Execute this command to run linter:

```bash
$ cd my_project/
$ composer lint
```


[1]: https://symfony.com/doc/current/best_practices.html
[2]: https://symfony.com/doc/current/setup.html#technical-requirements
[3]: https://symfony.com/doc/current/setup/web_server_configuration.html
[4]: https://symfony.com/download
[5]: https://symfony.com/book
[6]: https://getcomposer.org/
