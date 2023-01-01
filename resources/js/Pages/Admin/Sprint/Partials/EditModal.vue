<template>
    <div class="">
      <!-- <a-button type="primary" @click="showModal">Edit sprint</a-button> -->
      <a-button @click="showModal" type="primary" shape="circle">
        <template #icon>
            <edit-outlined />
        </template>
    </a-button>

      <a-modal  v-model:visible="visible" title="Basic Modal" @ok="handleOk">
          <section class="flex flex-col space-y-4">
          <div>
              <a-input v-model:value="sprint" placeholder="sprint name" />
          </div>
  
          <div>
              <span>degree: </span>
              <a-input-number v-model:value="degree" :min="1" :max="10" />
          </div>
  
          <div>
              <a-select
              v-model:value="selectTasks"
              mode="multiple"
              style="width: 100%"
              placeholder="Please select Tasks Associated with this sprint"
              :options="tasks"
              @change="handleChange"
              >
          </a-select>
          </div>
  
  
          <div>
              <span>start date</span>
              <datetime v-model="startDate"
              type="datetime"
              format="YYYY-MM-DD HH:mm:ss"
              display-format="dd jDD jMMMM jYYYY  HH:mm:ss"
              ></datetime>
          </div>
  
          <div>
              <span>deadline date</span>
              <datetime v-model="endDate"
              type="datetime"
              format="YYYY-MM-DD HH:mm:ss"
              display-format="dd jDD jMMMM jYYYY  HH:mm:ss"
              ></datetime>
          </div>
          </section>
  
  
      </a-modal>
    </div>
  </template>
  
  <script>
  import { notification } from 'ant-design-vue';
  import axios from 'axios';
  import VuePersianDatetimePicker from 'vue3-persian-datetime-picker'
  import { EditOutlined } from '@ant-design/icons-vue';

      export default {
          props: ['id', 'name', 'projectTitle'],
  
          data()  {
              return {
                  visible: false,
                  sprint: '',
                  startDate: '',
                  endDate: '',
                  tasks: [],
                  selectTasks: [],
                  sprintId: this.id,
                  degree: ''
              }
          },
          methods: {
                  handleOk(){
                      // this.visible = false;
                      console.log(this.selectTasks)
  
                      axios.post(`/api/v1/sprint/store/${this.projectId}`, 
                      {
                          tasks: this.selectTasks, 
                          sprint: this.sprint, 
                          startDate: this.startDate, 
                          endDate: this.endDate, 
                          degree: this.degree
                      })
                      .then((response)=> {
                              notification.success({
                              message: response.data.message,
                          });
                      })
                      .catch((error) => {
  
                          // console.log(this.$page.props)
                          notification.error({
                              message: error.response.data.message,
                          });
                      }) ;
                  },
                  showModal(){ 
                      this.visible  = true;
                      // console.log(this.projectId);
                      
                      this.$nextTick(() => {
                          axios.get(`/api/v1/sprint/index/${this.sprintId}`).then((response) => {
                              console.log(response.data);
                              const data = response.data.data;
                            //   console.log
                              this.selectTasks = data.selectedTasks;
                              this.tasks = data.availableTasks;
                              this.startDate = data.started_at;
                              this.endDate  = data.deadline_at
                              this.degree = data.degree;
                              this.sprint = data.sprint
                          });
                      })
  
                  },
                  handleChange(value, payload){
                      this.selectTasks = payload
                  }
              },
          mounted(){
  
          },
          components: {
              'datetime': VuePersianDatetimePicker,
              EditOutlined
          }
      }
  </script>