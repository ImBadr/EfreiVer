<template>
  <div>
    <NavBar />

    <div>
      <hr>
      <span class="badge bg-primary">Primary</span>
      <span class="badge bg-secondary">Secondary</span>
      <span class="badge bg-success">Success</span>
      <span class="badge bg-danger">Danger</span>
      <span class="badge bg-warning text-dark">Warning</span>
      <span class="badge bg-info text-dark">Info</span>
      <span class="badge bg-light text-dark">Light</span>
      <span class="badge bg-dark">Dark</span>
      <hr>
      <span class="badge rounded-pill bg-primary">Primary</span>
      <span class="badge rounded-pill bg-secondary">Secondary</span>
      <span class="badge rounded-pill bg-success">Success</span>
      <span class="badge rounded-pill bg-danger">Danger</span>
      <span class="badge rounded-pill bg-warning text-dark">Warning</span>
      <span class="badge rounded-pill bg-info text-dark">Info</span>
      <span class="badge rounded-pill bg-light text-dark">Light</span>
      <span class="badge rounded-pill bg-dark">Dark</span>
      <hr>
      <button type="button" class="btn btn-primary">Primary</button>
      <button type="button" class="btn btn-secondary">Secondary</button>
      <button type="button" class="btn btn-success">Success</button>
      <button type="button" class="btn btn-danger">Danger</button>
      <button type="button" class="btn btn-warning">Warning</button>
      <button type="button" class="btn btn-info">Info</button>
      <button type="button" class="btn btn-light">Light</button>
      <button type="button" class="btn btn-dark">Dark</button>
      <button type="button" class="btn btn-link">Link</button>
      <hr>
      <button type="button" class="btn btn-outline-primary">Primary</button>
      <button type="button" class="btn btn-outline-secondary">Secondary</button>
      <button type="button" class="btn btn-outline-success">Success</button>
      <button type="button" class="btn btn-outline-danger">Danger</button>
      <button type="button" class="btn btn-outline-warning">Warning</button>
      <button type="button" class="btn btn-outline-info">Info</button>
      <button type="button" class="btn btn-outline-light">Light</button>
      <button type="button" class="btn btn-outline-dark">Dark</button>
      <hr>
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <div class="spinner-border text-secondary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <div class="spinner-border text-success" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <div class="spinner-border text-danger" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <div class="spinner-border text-warning" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <div class="spinner-border text-info" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <div class="spinner-border text-dark" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <hr>
      <div class="spinner-grow text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <div class="spinner-grow text-secondary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <div class="spinner-grow text-success" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <div class="spinner-grow text-danger" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <div class="spinner-grow text-warning" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <div class="spinner-grow text-info" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <div class="spinner-grow text-light" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <div class="spinner-grow text-dark" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <hr>
    </div>

    <Footer />
  </div>
</template>

<script>
import NavBar from "./NavBar";
import Footer from "./Footer";

export default {
  name: "Default",
  components: {NavBar, Footer}
}
</script>
