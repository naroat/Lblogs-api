<?php


namespace App\Packages\Core\src\Traits;


trait Response
{
    /**
     * 响应
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseJson(array $data = [], array $list = [], int $code = 200)
    {
        //响应数据
        $responseData = [
            'status' => $this->status ?? true,
            //'errmsg' => $this->errmsg,
            //'errno' => $this->errno,
            'data' => empty($data) ? null : $data,
            'list' => empty($list) ? [] : $list,
        ];

        //!config('app.debug') ? false : $response_data['run_time'] = get_run_time() . ' ms';

        return response()->json($responseData, $code, [], JSON_UNESCAPED_UNICODE);
    }
}
