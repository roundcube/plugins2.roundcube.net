<template>
<div class="block pt-4">
  <h2>Getting Started</h2>
  <div>
    <p>
      The plugin repository for Roundcube is based on <a href="http://getcomposer.org/">Composer</a>
      to manage, install and update the plugins for your Roundcube installation. The following two
      steps are necessary to connect your local Roundcube installation to the plugin repository:
    </p>

    <h3>1. Install Composer</h3>
    <p>Run this in your command line:</p>
    <pre>
<code>cd &lt;roundcube-project-root&gt;
curl -s https://getcomposer.org/installer | php
</code>
</pre>

    <p>Or <a href="http://getcomposer.org/download/">download composer.phar</a> into your Roundcube root directory.</p>

    <h3>2. Configure composer.json</h3>
    <p>Copy the template <code>composer.json-dist</code> to <code>composer.json</code> in your Roundcube root directory.</p>

    <h3>3. Installing plugins</h3>
    <p><a href="https://packagist.org/?type=roundcube-plugin">Browse</a> through our repository and find the plugins you want to install.</p>
    <p>Then edit your local composer.json file and add the <code>"vendor/plugin"</code> names to the
      <code>"require"</code> section of the JSON structure. Don't forget to specify the version constraint:
    </p>
<pre><code>"require" : {
    ...,
    "roundcube/rcsample": "&gt;=0.2.0"
}</code></pre>
    <p>After every change to composer.json run</p>
    <pre><code>php composer.phar install</code></pre>
    <p>to download and install all dependencies.</p>
    <p>
      The plugins are installed to the <code>plugins/</code> directory and when running the install
      command you'll be asked whether to activate the plugins right away. For any later activation
      or deactivation of a plugin, you can add/remove the plugin name to/from the <code>$config['plugins']</code>
      option in your local Roundcube config file config/config.inc.php.
    </p>
  </div>
</div>
</template>

<script>
export default {
  name: 'GettingStarted',
  props: {

  }
}
</script>

<style scoped lang="less">
.block {

}
</style>
