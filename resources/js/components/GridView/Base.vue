<script>
export default {
  name: 'ListBase',
  data() {
    return {
      pager: { page: 1, pageSize: 10 },
    };
  },
  methods: {
    // ----------- interface methods ----------------
    getBasicRoute() {
      // TODO: should be overrided by children
      return '';
    },
    getResource() {
      // TODO: should be overrided by children
      return null;
    },
    reloadList() {
      // TODO: should be overrided by children
    },

    // -------------- reusable methods -----------------
    getEditRoute(basicRoute, row) {
      return '/' + basicRoute + '/edit/' + row.id;
    },
    getViewRoute(basicRoute, row) {
      return '/' + basicRoute + '/detail/' + row.id;
    },
    getAddRoute(basicRoute) {
      return '/' + basicRoute + '/add/';
    },
    handleView(row) {
      this.$nextTick(() => this.$router.push({
        path: this.getViewRoute(this.getBasicRoute(), row),
      }));
    },
    handleEdit(row) {
      this.$nextTick(() => this.$router.push({
        path: this.getEditRoute(this.getBasicRoute(), row),
      }));
    },
    handleAdd() {
      this.$nextTick(() => this.$router.push({
        path: this.getAddRoute(this.getBasicRoute()),
      }));
    },
    handleDelete(row) {
      this.$confirm('此操作将删除【' + row.name + '】, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
      }).then(async() => {
        const resource = this.getResource();
        if (!resource) {
          return;
        }
        const { code, msg, errors } = await resource.destroy(row.id);
        this.showResult(code, msg, errors);
        this.reloadList();
        this.$emit('delete', row);
      }).catch(() => {
        this.showResult(500, '删除操作异常');
      });
    },
    handleBatchUpdate(rows, data) {
      if (rows.length === 0) {
        this.$message('请勾选要更新的记录');
        return;
      }
      const resource = this.getResource();
      if (!resource) {
        return;
      }

      this.$confirm('确定更新所选记录?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
      }).then(async() => {
        const ids = rows.map(item => {
          return item.id;
        });
        const { code, msg, errors } = await resource.batchUpdate(ids, data);
        this.showResult(code, msg, errors);
        this.reloadList();
      }).catch(() => {
        this.showResult(500, '批量更新异常');
      });
    },
    handleBatchDelete(rows) {
      if (rows.length === 0) {
        this.$message('请勾选要删除的记录');
        return;
      }
      const resource = this.getResource();
      if (!resource) {
        return;
      }

      this.$confirm('此操作将删除所有勾选的记录, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
      }).then(async() => {
        const ids = rows.map(item => {
          return item.id;
        });
        const { code, msg, errors } = await resource.batchDestroy(ids);
        this.showResult(code, msg, errors);
        this.reloadList();
      }).catch(() => {
        this.showResult(500, '批量删除异常');
      });
    },
    showResult(code, msg, errors) {
      const success = code === 200;

      if (success) {
        this.$message({
          message: msg || '操作成功',
          type: 'success',
        });
        return;
      }

      msg = msg || '操作失败';
      if (errors) {
        for (var key in errors) {
          msg += '<br/>' + errors[key];
        }
      }
      this.$message({
        dangerouslyUseHTMLString: true,
        message: msg,
        type: 'error',
      });
    },
    /**
     * 获取页面id参数
     */
    getId() {
      return parseInt(this.$route.params && this.$route.params.id);
    },
  },
};
</script>
