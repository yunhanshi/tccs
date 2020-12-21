import Layout from '@/layout';

const todoRoutes = {
  path: '/todo',
  component: Layout,
  redirect: '/todo/list',
  name: 'todo',
  meta: {
    title: 'Todo List',
    icon: 'dashboard',
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/todo/TodoList'),
      name: 'todo-list',
      meta: {
        title: 'todo',
      },
    },
  ],
};
export default todoRoutes;
