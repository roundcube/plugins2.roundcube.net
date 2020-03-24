<template>
  <body class="d-flex flex-column h-100">
    <b-navbar toggleable="lg" type="dark" variant="dark" fixed="top">
      <b-navbar-brand to="/">
        <img src="./assets/roundcube_logo_icon.svg" class="nav-logo" width="40" height="40" alt="Roundcube - open source webmail software">
        <span class="title">Roundcube Plugins</span>
      </b-navbar-brand>

      <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

      <b-navbar-nav class="d-none d-md-block">
        <span class="nav-link claim">The Official Plugin Repository</span>
      </b-navbar-nav>

      <b-collapse id="nav-collapse" is-nav>
        <b-navbar-nav class="ml-auto">
          <b-nav-item to="/about/" exact exact-active-class="active">About</b-nav-item>
          <b-nav-item href="https://packagist.org/?type=roundcube-plugin">Browse</b-nav-item>
          <b-nav-item href="https://packagist.org/packages/submit">Submit</b-nav-item>
        </b-navbar-nav>

      </b-collapse>
    </b-navbar>

    <router-view/>
  </body>
</template>

<style lang="less">
main {
  padding-top: 66px;
}

.navbar {
  .navbar-brand {
    .nav-logo {
      margin-right: 15px;
    }
  }
}

.claim {
  font-style: italic;
  font-size: 90%;
}

.h4, .h5, .h6, h4, h5, h6 {
  margin-top: 10px;
  margin-bottom: 10px;
}

.h1, h1 {
  font-size: 2.25rem;
}

.h2, h2 {
  font-size: 1.75rem;
}

.h3, h3 {
  font-size: 1.5rem;
}

.h5, h5 {
  font-size: 14px;
}

pre {
  line-height: 12px;
  font-size: 12px;
  padding: 9.5px;
  margin: 0 0 10px;
  border-left: 2px solid #2d2d32;
  word-break: break-all;
  word-wrap: break-word;
  background: #fbfbfb;
}
</style>
