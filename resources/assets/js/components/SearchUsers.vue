<template>
  <div>
    <div class="modal-body-customer-info">
      <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
        <input :disabled="queryFirst.length != 0 || queryLast.length != 0" v-model="queryEmail" name="queryEmail" type="search" class="form-control" placeholder="Email">
      </div>
      <p class="input-divider">- OR -</p>
      <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-user"></i></span>
        <input :disabled="queryEmail.length != 0" v-model="queryFirst" name="queryFirst" type="search" class="form-control" placeholder="First Name">
      </div>
      <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-user"></i></span>
        <input :disabled="queryEmail.length != 0" v-model="queryLast" name="queryLast" type="search" class="form-control" placeholder="Last Name">
      </div>
      <button style="margin-top: 10px;" :disabled="queryFirst.length == 0 && queryLast.length == 0 && queryEmail.length == 0" class="btn btn-block btn-primary" v-on:click="searchUsers()">Search</button>
    </div>
    <div class="modal-footer" style="padding: 0px; text-align: center;">
      <table class="table" style="margin-bottom: 0px;" v-if="this.search.length != 0 && this.search != this.notFound">
        <tbody>
          <tr v-for="person in this.search">
            <td v-if="person.photo_path"><img class="searchPhoto" :src="person.photo_path" alt="User Photo"></td>
            <td v-else><img class="searchPhoto" src="/images/icon-profile-photo.png"></td>
            <td class="searchTableData">{{ person.first_name }} {{ person.last_name }}</td>
            <td class="searchTableData">{{ person.email }}</td>
            <td><button class="btn btn-success" v-on:click="addUser(person)">Add</button></td>
          </tr>
        </tbody>
      </table>
      <h5 v-if="this.search == this.notFound" class="noResult">{{ this.notFound }}</h5>
    </div>
  </div>
</template>

<script>

  export default {
    props: ['profile', 'id'],

    data() {
      return {
        queryEmail: '',
        queryFirst: '',
        queryLast: '',
        search: [],
        notFound: 'User not found'
      }
    },

    methods: {

      refresh({data}) {
        if (data.users.length !== 0) {
          var users = [];
          data.users.forEach(function(user) {
            users.push(user);
          });
          this.search = users;
        } else {
          this.search = this.notFound;
        }
      },

      updateEmployees({data}) {
        VueEvent.fire('addEmployee', data);
        this.resetSearch();
        $('#' + this.id).modal('hide');
      },

      resetSearch() {
        this.queryEmail = '';
        this.queryFirst = '';
        this.queryLast = '';
        this.search = [];
      },

      searchUsers() {
        this.search = [];
        if (this.queryEmail.length != 0) {
          axios.get('/api/web/users/' + this.profile.slug + '/search?email=' + this.queryEmail)
            .then(this.refresh);
        
        } else {
          axios.get('/api/web/users/' + this.profile.slug + '/search?first=' + this.queryFirst + '&last=' + this.queryLast)
            .then(this.refresh);
        }
      },

      addUser(user) {
        axios.patch('/api/web/users/' + this.profile.slug + '/' + user.id, {
          'employer_id': this.profile.id
        })
          .then(this.updateEmployees);
      }
    }
  }
</script>