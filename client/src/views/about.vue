<template>
  <main class="flex-shrink-0 container pb-5">
    <div class="block pt-4">
      <h1>About Roundcube plugins</h1>

      <p>
        Plugins are installed with <a href="https://getcomposer.org/">Composer</a>
        and are published to <a href="https://packagist.org/">packagist.org</a> which
        is the default repository for PHP packages and the primary source for Composer.
      </p>

      <p>
        This site is the former plugin repository for Roundcube webmail and now
        serves as a minimalist frontend to find Roundcube plugins at packagist.org.
      </p>

      <h2>How to write plugins</h2>
      <p>
        Roudcube plugins are basically uniquely named folders with some conventions about their contents.
        Read the full documentation <a href="https://github.com/roundcube/roundcubemail/wiki/Plugin-API#how-to-write-a-plugin">How to Write a Plugin</a>
        in our Github wiki.
      </p>
      <p>
        We suggest to use Composer to install plugins and therefore Roundcube plugins can be published
        as a Composer package of the type <code>roudcube-plugin</code>. All it takes for that is to
        create a <code>composer.json</code> file inside the plugin folder and pusbilish it in a public
        source code repository. For more details about the <code>composer.json</code> file and how to
        add them to our plugin repository, just read on.
      </p>

      <h2>How to submit plugins</h2>
      <p>
        Roundcube plugins need to be submitted to <a href="https://packagist.org/">packagist.org</a>.
        Therefore, plugin developers are required to create an account there and then follow
        the instructions <a href="https://packagist.org/about">How to submit packages?</a>
      </p>

      <p>
        When creating your plugin's <code>composer.json</code> file, just make sure to
        set <code>"type": "roundcube-plugin"</code>. The <code>extra</code> section
        allows to define Roudcube specific settings.
      </p>

      <p>
        This is what a typical <code>composer.json</code> file for Roundcube plugins looks like:
      </p>
      <pre><code>{
    "name": "roundcube/archive",
    "type": "roundcube-plugin",
    "description": "Archive feature for Roundcube",
    "keywords": ["mail","archive"],
    "homepage": "http://github.com/roundcube/roundcubemail",
    "license": "GPL-3.0+",
    "authors": [
        {
            "name": "Thomas Bruederli",
            "email": "thomas@roundcube.net",
            "homepage": "http://roundcube.net",
            "role": "Developer"
        }
    ],
    "require": {
        "php": ">=5.3.0",
        "roundcube/plugin-installer": ">=0.1.6"
    },
    "extra": {
        "roundcube": {
            "min-version": "0.9.0",
            "max-version": "10.21"
        }
    }
}</code></pre>

      <p>
        The complete schema description can be found in the
        <a href="https://getcomposer.org/doc/04-schema.md">Composer documentation</a>.
      </p>

      <h3>Roundcube version constraints</h3>
      <p>
        Plugins, including specific versions of plugins, can define min and max versions of Roundcube webmail
        they are made for. Because the Roundcube codebase continously evolves and internal functions and APIs
        may change over time, this is how plugin developers can express what version of Roundcube a plugin
        is built for and tested with. Either value is optional and can be omitted.
      </p>

      <pre><code>"extra": {
    "roundcube": {
        "min-version": "0.9.0",
        "max-version": "1.3.99"
    }
}</code></pre>

      <h3>Database schema initialization and upgrading</h3>
      <p>
        In case you plugin requires additional tables in the Roundcube database, the plugin installer
        can take care of their initialization when installing the plugin as well as keeping the schema up-to-date.
        This is done by specifying the sql-dir option in the <code>extra/roundcube</code> section.
        The value should be a relative path to a directory holding the SQL initialization and update files:
      </p>
      <pre><code>"extra": {
    "roundcube": {
        "sql-dir": "schema/SQL"
    }
}</code></pre>

      <p>
        The SQL directory should contain the schema files for initialization named with <code>&lt;driver&gt;.initial.sql</code>
        as well as subdirectories for each driver (e.g. mysql) holding schema update scripts named with a numeric
        value representing their creation date in the format 'Ymd00'. Example:
      </p>

      <pre>schema/
    SQL/
        mysql/
            2014022500.sql
            2014121600.sql
        postgres/
            2014022500.sql
            2014121600.sql
        sqllite/
            2014121600.sql
        mysql.initial.sql
        postgres.initial.sql
        sqlite.initial.sql</pre>

      <p>
        On plugin installation with a Mysql database driver configured, the file <code>schema/SQL/mysql.initial.sql</code>
        will be run against Roundcube's database connection. When the plugin is updated, all files with a name higher than
        the database initialization/update date from within the folder <code>schema/SQL/mysql/</code> will be executed.
      </p>

      <h3>Plugin install/upgrade/uninstall scripts</h3>
      <p>
        If the installation or upgrading of a plugin requires some additional things to be done
        (e.g. creating a temp folder or installing cron jobs), arbitrary shell scripts can be shipped with the
        plugin package and registered for execution after an install, update or uninstall operation:
      </p>

      <pre><code>"extra": {
    "roundcube": {
        "post-install-script": "bin/install.sh",
        "post-update-script": "bin/update-me.sh",
        "post-uninstall-script": "bin/uninstall.sh"
    }
}</code></pre>

      <p>
        The paths for the <code>post-*-script</code> options are relative to the plugin directory.
        Scripts can either be shell script or PHP files and will be executed with the privileges of
        the user who executes the plugin installation (i.e. running composer).
      </p>
    </div>
  </main>
</template>

<style scoped lang="less">
  h3 {
    margin-top: 1em;
    font-size: 1.3rem;
  }
</style>
