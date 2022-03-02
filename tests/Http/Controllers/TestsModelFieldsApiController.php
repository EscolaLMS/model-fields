<?php

namespace EscolaLms\ModelFields\Tests\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use EscolaLms\ModelFields\Tests\Http\Resources\UserResource;
use EscolaLms\ModelFields\Tests\Http\Resources\UserAdminResource;

use EscolaLms\ModelFields\Tests\Models\User;
use EscolaLms\ModelFields\Tests\Http\Requests\UserCreateRequest;

class TestsModelFieldsApiController extends Controller
{

    public function create(UserCreateRequest $request): JsonResponse
    {
        $input = $request->all();
        $result = User::create($input);
        return response()->json($result);
    }

    public function list(Request $request): JsonResponse
    {
        $users = User::all();
        $result = UserResource::collection($users);
        return response()->json($result);
    }

    public function adminList(Request $request): JsonResponse
    {
        $users = User::all();
        $result = UserAdminResource::collection($users);
        return response()->json($result);
    }
}
