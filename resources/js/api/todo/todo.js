import Resource from '@/api/resource';
import request from '@/utils/request';

class TodoResource extends Resource {
  constructor() {
    super('todos');
  }
  finish(id) {
    return request({
      url: '/finish' + this.uri + '/' + id,
      method: 'put',
    });
  }
  redo(id) {
    return request({
      url: '/redo' + this.uri + '/' + id,
      method: 'put',
    });
  }
  burndown() {
    return request({
      url: '/burndown/',
      method: 'get',
    });
  }
}

export { TodoResource as default };
