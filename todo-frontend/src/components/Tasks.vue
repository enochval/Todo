<template>
  <div>
    <v-container grid-list-xl text-xs-center>
      <v-layout row wrap>
        <v-flex xs10 offset-xs1>
          <v-card color="blue">
            <v-dialog v-model="dialog" max-width="500px">
              <v-btn color="success" dark slot="activator" class="mb-2">New To-Do</v-btn>
              <v-card>
                <v-card-title>
                  <span class="headline">{{ formTitle }}</span>
                </v-card-title>
                <v-card-text>
                  <v-container grid-list-md>
                    <v-layout wrap>
                      <v-flex xs12 sm8 md6>
                        <v-text-field label="Title" v-model="editedItem.title"></v-text-field>
                      </v-flex>
                    </v-layout>
                    <v-layout wrap>
                      <v-flex xs12 sm8 md6>
                          <v-text-field label="Description" v-model="editedItem.description" textarea></v-text-field>
                      </v-flex>
                    </v-layout>
                  </v-container>
                </v-card-text>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="blue darken-1" flat @click.native="close">Cancel</v-btn>
                  <v-btn color="blue darken-1" flat @click.native="update" v-if="editedIndex !== -1">Update Task</v-btn>
                  <v-btn color="blue darken-1" flat @click.native="save" v-else>Create Task</v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
            <v-data-table
              :headers="headers"
              :items="items"
              hide-actions
              class="elevation-1"
            >
              <template slot="items" slot-scope="props">
                <td>{{ props.item.title }}</td>
                <td >{{ props.item.description }}</td>
                <td >{{ props.item.created_at }}</td>
                <td class="justify-center layout px-0">
                  <v-btn icon class="mx-0" @click="editItem(props.item)">
                    <v-icon color="teal">edit</v-icon>
                  </v-btn>
                  <v-btn icon class="mx-0" @click="deleteItem(props.item)">
                    <v-icon color="pink">delete</v-icon>
                  </v-btn>
                </td>
              </template>
              <template slot="no-data">
                <v-btn color="danger" @click="fetchTasks">Reset</v-btn>
              </template>
            </v-data-table>
          </v-card>
        </v-flex>
      </v-layout>
    </v-container>
  </div>
</template>

<script>
  export default {
    data: () => ({
      dialog: false,
      headers: [
        {
          text: 'Title',
          align: 'center',
          sortable: false,
          value: 'title'
        },
        { text: 'Description', align: 'center', value: 'description' },
        { text: 'Date Created', align: 'center', value: 'created_at' },
        { text: 'Actions', value: 'name', sortable: false }
      ],
      items: [],
      editedIndex: -1,
      editedItem: {
        title: '',
        description: '',
      },
      defaultItem: {
        title: '',
        description: '',
      }
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'New Task' : 'Edit Task'
      }
    },

    watch: {
      dialog (val) {
        val || this.close()
      }
    },

    created () {
      this.fetchTasks();
    },

    methods: {
      fetchTasks(){
        axios.get('/tasks')
          .then(({data})=>{
            this.items = data;
          })
          .catch(({response})=>console.log(response.data.error))
      },

      editItem (item, ) {
        this.editedIndex = this.items.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      deleteItem (item) {
        const index = this.items.indexOf(item)
        confirm('Are you sure you want to delete this item?') && this.items.splice(index, 1)
        axios.delete('/task/'+item.id).then(({data})=>{
          this.fetchTasks();
        }).catch(({response})=>console.log(response.data.errors))
      },

      close () {
        this.dialog = false
        setTimeout(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        }, 300)
      },

      update () {
        if (this.editedIndex > -1) {
          Object.assign(this.items[this.editedIndex], this.editedItem)
        } else {
          this.items.push(this.editedItem)
        }
        axios.patch('/task/'+this.editedItem.id, this.editedItem).then(({data})=>{
          this.close();
        }).catch(({response})=>console.log(response.data.errors))
      },

      save(){
        axios.post('/task', this.editedItem)
          .then(({data})=>{
            this.items.push(data.task)
            this.close()
          })
          .catch(({response})=>console.log(response.data.errors))
      }
    }
  }
</script>
