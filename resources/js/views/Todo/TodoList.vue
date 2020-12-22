<template>
  <div>
    <el-row style="background:#fff;padding:16px 16px 0;margin-bottom:32px;">
      <div style="color:#f44336;font-size:12px;margin-left:30px;">This chart auto refresh per 30s</div>
      <burndown-chart ref="burndownChart" />
    </el-row>
    <grid-view ref="todoGridView" :columns="columns" :buttons="buttons" :pager="pager" :filters="filters" :resource="resource" />
    <el-dialog :visible.sync="dialogVisible" :title="dialogType==='edit'?'Edit Task':'New Task'" width="80%">
      <el-form ref="todoForm" :model="todoForm" :rules="todoRules" label-width="200px" label-position="left">
        <el-form-item label="Task Name" prop="task">
          <el-input v-model="todoForm.task" placeholder="Role Name" />
        </el-form-item>
        <el-form-item label="Description">
          <el-input
            v-model="todoForm.description"
            type="textarea"
            placeholder="Task Description"
          />
        </el-form-item>
      </el-form>
      <div style="text-align:right;">
        <el-button type="danger" @click="dialogVisible=false">Cancel</el-button>
        <el-button type="primary" @click="confirmTodoForm">Confirm</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import TodoResource from '@/api/todo/todo.js';
import GridView from 'Components/GridView';
import ListBase from 'Components/GridView/Base';
import BurndownChart from './BurndownChart';

const todoResource = new TodoResource();
const defaultTodo = {
  id: '',
  task: '',
  description: '',
};

export default {
  name: 'TodoList',
  components: {
    GridView,
    BurndownChart,
  },
  extends: ListBase,
  data() {
    return {
      todoForm: Object.assign({}, defaultTodo),
      todoRules: {
        task: [
          { required: true, message: 'Please enter task name', trigger: 'blur' },
          { min: 5, max: 50, message: '5 to 50 characters in length', trigger: ['blur', 'change'] },
        ],
      },
      reload: true,
      filters: [
        { type: 'input', label: 'Task', queryKey: 'task@like', span: 7 },
        { type: 'date-picker', label: 'Created at', queryKey: 'created_at', span: 10 },
      ],
      buttons: [
        { type: 'success', label: 'New Task', clickEvent: this.handleAdd },
      ],
      columns: [
        { id: 'todo-created-at', type: 'string', label: 'Created at', prop: 'created_at', sortable: 'custom' },
        { id: 'todo-task', type: 'link', label: 'Task', prop: 'task', sortable: 'custom', clickEvent: this.handleEdit },
        { id: 'todo-description', type: 'string', label: 'Description', prop: 'description' },
        { id: 'todo-finished-at', type: 'string', label: 'Finished at', prop: 'finished_at', sortable: 'custom' },
        { id: 'todo-buttons', type: 'button', label: 'Options', prop: 'title', minWidth: '100px',
          buttons: [
            { name: 'Edit task', icon: 'el-icon-edit', type: 'primary', class: '', style: '', clickEvent: this.handleEdit },
            { name: 'Finish', icon: 'el-icon-finished', type: 'success', class: '', rowInvisible: 'status', clickEvent: this.handleFinish },
            { name: 'Redo', icon: 'el-icon-refresh', type: 'warning', class: '', rowVisible: 'status', clickEvent: this.handleRedo },
            { name: 'Delete', icon: 'el-icon-delete', type: 'danger', clickEvent: this.handleDelete },
          ],
        },
      ],
      resource: todoResource,
      dialogVisible: false,
      dialogType: 'new',
    };
  },
  created() {
  },
  methods: {
    getBasicRoute() {
      return 'todo';
    },
    getResource() {
      return todoResource;
    },
    reloadData() {
      this.$refs.burndownChart.initChart();
      this.$refs.todoGridView.handleFilter();
    },
    handleAdd() {
      this.todoForm = Object.assign({}, defaultTodo);
      this.dialogType = 'new';
      this.dialogVisible = true;
    },
    handleEdit(row) {
      this.todoForm = {
        id: row.id,
        task: row.task,
        description: row.description,
      };
      this.dialogType = 'edit';
      this.dialogVisible = true;
    },
    confirmTodoForm() {
      this.$refs.todoForm.validate(async valid => {
        if (valid) {
          const isEdit = this.dialogType === 'edit';
          const { code, msg } = isEdit ? await todoResource.update(this.todoForm.id, this.todoForm) : await todoResource.store(this.todoForm);
          if (code !== 200) {
            this.$message({
              message: msg || ('Fail to ' + this.dialogType + ' task'),
              type: 'error',
            });
          } else {
            this.$message({
              message: msg || ('Success to ' + this.dialogType + ' task'),
              type: 'success',
            });
            this.dialogVisible = false;
            this.reloadData();
          }
        } else {
          console.log('error submit!');
          return false;
        }
      });
    },
    handleDelete(row) {
      this.$confirm('Are you sure to DELETE task [' + row.task + '] ?', 'Notice', {
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        type: 'warning',
      }).then(async() => {
        const { code, msg } = await todoResource.destroy(row.id);
        if (code !== 200) {
          this.$message({
            message: msg || ('Fail to delete task'),
            type: 'error',
          });
        } else {
          this.$message({
            message: msg || ('Success to delete task'),
            type: 'success',
          });
          this.reloadData();
        }
      });
    },
    handleFinish(row) {
      this.$confirm('Are you sure to FINISH task [' + row.task + '] ?', 'Notice', {
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        type: 'warning',
      }).then(async() => {
        const { code, msg } = await todoResource.finish(row.id);
        if (code !== 200) {
          this.$message({
            message: msg || ('Fail to finish task'),
            type: 'error',
          });
        } else {
          this.$message({
            message: msg || ('Success to finish task'),
            type: 'success',
          });
          this.reloadData();
        }
      });
    },
    handleRedo(row) {
      this.$confirm('Are you sure to REDO task [' + row.task + '] ?', 'Notice', {
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        type: 'warning',
      }).then(async() => {
        const { code, msg } = await todoResource.redo(row.id);
        if (code !== 200) {
          this.$message({
            message: msg || ('Fail to redo task'),
            type: 'error',
          });
        } else {
          this.$message({
            message: msg || ('Success to redo task'),
            type: 'success',
          });
          this.reloadData();
        }
      });
    },
  },
};
</script>
