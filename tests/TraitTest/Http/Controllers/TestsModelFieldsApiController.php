<?php

namespace EscolaLms\ModelFields\Tests\TraitTest\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use EscolaLms\ModelFields\Tests\TraitTest\Http\Resources\UserResource;
use EscolaLms\ModelFields\Tests\TraitTest\Http\Resources\UserAdminResource;

use EscolaLms\ModelFields\Tests\TraitTest\Models\User;
use EscolaLms\ModelFields\Tests\TraitTest\Http\Requests\UserCreateRequest;

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
