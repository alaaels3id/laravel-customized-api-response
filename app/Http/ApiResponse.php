<?php

namespace App\Http;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function response($data = null, $type = 'success', $pagination = null, $msg = '', $code = 200, $err = ''): JsonResponse
    {
        $response = ['data' => $data, 'status' => true, 'message' => $msg, 'error' => $err];

        if($type == 'success')
        {
            if($pagination && isset($pagination->total))
            {
                $res['data'] = $data;
                $res['pagination']['total'] = $pagination->total;
                $res['pagination']['perPage'] = $pagination->perPage;
                $res['pagination']['last_page'] = $pagination->last_page;
                $res['pagination']['currentPage'] = $pagination->currentPage;
            }
            else
            {
                $res = $data;
            }

            $response['data'] = $res;

            $response['message'] = ($msg == '') ? 'Request done successfully' : $msg;
        }

        if($type == 'warning' || $type == 'fails' || $type == 'unAuth') $response['status'] = false;

        return response()->json($response, $code);
    }

    public static function pagination($collection, $resource): JsonResponse
    {
        $pagination = self::paginated($collection);

        $data = $resource::collection($pagination->paginated);

        return self::response($data,'success', $pagination);
    }

    public static function warning($message = '', $data = null): JsonResponse
    {
        return self::response($data,'warning',null, $message,400);
    }

    public static function success($message = '', $data = null): JsonResponse
    {
        return self::response($data,'success',null, $message);
    }

    public static function validation($message, $data = null): JsonResponse
    {
        return self::response($data,'warning',null, $message,422);
    }

    public static function fails($message = '', $data = null): JsonResponse
    {
        $msg = ($message == '') ? 'Server Error 500' : $message;

        return self::response($data,'fails',null, $msg,500);
    }

    public static function unAuth($message = 'Unauthenticated', $data = null): JsonResponse
    {
        return self::response($data,'unAuth',null, $message,401);
    }

    public static function authFails($code = 400, $data= null): JsonResponse
    {
        return self::response($data,'unAuth',null, trans('auth.failed'), $code);
    }

    private static function paginated($collection, $perPage = 10)
    {
        $page = (int)request('page');

        $start = ($page == 1) ? 0 : ($page - 1) * $perPage;

        $total = $collection->count();

        $pages_count = (int)$total / (int)$perPage;

        $page_counter = is_double($pages_count) ? (int)$pages_count + 1 : $pages_count;

        if($page <= 0) return (object)['paginated' => $collection];

        $data['total'] = $total;

        $data['paginated'] = $collection->slice($start, $perPage);

        $data['currentPage'] = $page;

        $data['perPage'] = $perPage;

        $data['last_page'] = $pages_count >= 1 ? $page_counter : 1;

        return (object)$data;
    }
}
