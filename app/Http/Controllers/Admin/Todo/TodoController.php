<?php

namespace App\Http\Controllers\Admin\Todo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\Todo\TodoService;
use Illuminate\Support\Arr;

class TodoController extends Controller
{
    private $todoService;

    public function __construct(TodoService $todoService)
    {
        $this->todoService = $todoService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index(Request $request)
    {
        $params = $request->all();

        $params['filter']['user_id'] = $request->user()->id;

        $res = $this->todoService->getList($params);

        if(!$res['success']) {
            return $this->json(Response::HTTP_BAD_REQUEST, 'Get todo list fail');
        }

        //add status
        foreach ($res['data'] as $key => $value) {
            if (empty($value['finished_at'])) {
                $res['data'][$key]['status'] = 0;
                $res['data'][$key]['finished_at'] = 'Unfinished';
            } else {
                $res['data'][$key]['status'] = 1;
            }
        }

        return $this->json(Response::HTTP_OK, null, $res['data'], Arr::get($res, 'meta', null));
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \App\Exceptions\Validation\PermissionException
     */
    public function show(Request $request, $id)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function store(Request $request)
    {
        $params = $request->all();
        unset($params['id']);
        $params['user_id'] = $request->user()->id;
        $res = $this->todoService->add($params);

        if(!$res['success']) {
            return $this->json(Response::HTTP_BAD_REQUEST, 'Fail to add new task');
        }

        return $this->json(Response::HTTP_OK, 'Success to add new task');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $params = $request->all();
        unset($params['id']);
        $res = $this->todoService->update($params, ['id' => $id, 'user_id' => $request->user()->id]);

        if(!$res['success']) {
            return $this->json(Response::HTTP_BAD_REQUEST, 'Fail to update task');
        }

        return $this->json(Response::HTTP_OK, 'Success to update task');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int  $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $res = $this->todoService->deleteByCnds(['id' => $id, 'user_id' => $request->user()->id]);

        if (!$res['success']) {
            return $this->json(Response::HTTP_BAD_REQUEST, 'Fail to update task');
        }

        return $this->json(Response::HTTP_OK, 'Success to delete task');
    }

    /**
     * finish task.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function finish(Request $request, $id)
    {
        $res = $this->todoService->update(['finished_at' => date('Y-m-d H:i:s')], ['id' => $id, 'user_id' => $request->user()->id]);

        if(!$res['success']) {
            return $this->json(Response::HTTP_BAD_REQUEST, 'Fail to finish task');
        }

        return $this->json(Response::HTTP_OK, 'Success to finish task');
    }

    /**
     * redo task.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function redo(Request $request, $id)
    {
        $res = $this->todoService->update(['finished_at' => null], ['id' => $id, 'user_id' => $request->user()->id]);

        if(!$res['success']) {
            return $this->json(Response::HTTP_BAD_REQUEST, 'Fail to redo task');
        }

        return $this->json(Response::HTTP_OK, 'Success to redo task');
    }

    /**
     * burndown chart data.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function burndown(Request $request)
    {
        $res = $this->todoService->getBurndown($request->user()->id);

        if(!$res['success']) {
            return $this->json(Response::HTTP_BAD_REQUEST, 'Fail to load burndown chart');
        }

        return $this->json(Response::HTTP_OK, 'Success to load burndown chart', $res['data']);
    }
}
