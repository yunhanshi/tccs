<?php

namespace Tests\Unit;

use App\Services\Todo\TodoService;
use PHPUnit\Framework\TestCase;

class TodoTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testTodoServiceGetBurdown()
    {
        $todoService = new TodoService();
        $res = $todoService->getBurndown(1);

        $this->assertTrue($res['success']);
        $this->assertNotEmpty($res['data']);

        $burndownData = $res['data'];
        $this->assertNotEmpty($burndownData['xAxis']);
        $this->assertCount(60, $burndownData['xAxis']);
        $this->assertNotEmpty($burndownData['chartData']);
        $this->assertCount(60, $burndownData['chartData']);
    }
}
