<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use SimpleXMLElement;
use Illuminate\Support\Facades\Validator;

class PostsController extends Controller
{
    // Validasi Accept Header (hanya application/json atau application/xml)
    // Validasi Accept Header (dilonggarkan agar default Postman tetap jalan)
    private function validateAccept(Request $request)
    {
        $accept = $request->header('Accept');

        // Jika Postman mengirim */* atau kosong, anggap minta JSON
        if ($accept === '*/*' || empty($accept)) {
            return 'application/json';
        }

        if ($accept !== 'application/json' && $accept !== 'application/xml') {
            return false;
        }
        return $accept;
    }

    // Validasi Content-Type Header (dilonggarkan)
    private function validateContentType(Request $request)
    {
        $contentType = $request->header('Content-Type');

        // Izinkan JSON, Form-urlencoded, atau kosong
        if ($contentType === 'application/json' || $contentType === 'application/x-www-form-urlencoded' || empty($contentType)) {
            return true;
        }
        return false;
    }

    // Helper penentu format response (JSON atau XML)
    private function buildResponse($data, $acceptHeader, $status = 200)
    {
        if ($acceptHeader === 'application/xml') {
            $xml = new SimpleXMLElement('<root/>');
            $arrayData = json_decode(json_encode($data), true);
            $this->arrayToXml($arrayData, $xml);
            return response($xml->asXML(), $status)->header('Content-Type', 'application/xml');
        }

        return response()->json($data, $status);
    }

    // Helper konversi array ke XML
    private function arrayToXml($data, &$xmlData)
    {
        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $key = 'item';
            }
            if (is_array($value)) {
                $subnode = $xmlData->addChild($key);
                $this->arrayToXml($value, $subnode);
            } else {
                $xmlData->addChild((string)$key, htmlspecialchars((string)$value));
            }
        }
    }

    // GET /posts (Read All)
    public function index(Request $request)
    {
        $accept = $this->validateAccept($request);
        if (!$accept) {
            return response('Not Acceptable!', 406);
        }

        $paginator = Post::orderBy("id", "DESC")->paginate(2);

        $response = [
            "total_count" => $paginator->total(),
            "limit" => $paginator->perPage(),
            "pagination" => [
                "next_page" => $paginator->nextPageUrl(),
                "current_page" => $paginator->currentPage()
            ],
            "data" => $paginator->items(), // Method items() ini yang ngambil data array-nya
        ];

        return $this->buildResponse($response, $accept, 200);
    }

    // GET /post/{id} (Read Detail) -> Tantangan #2
    public function show(Request $request, $id)
    {
        $accept = $this->validateAccept($request);
        if (!$accept) {
            return response('Not Acceptable!', 406);
        }

        $post = Post::find($id);
        if (!$post) {
            abort(404);
        }

        return $this->buildResponse($post, $accept, 200);
    }

    // POST /posts (Create) -> Tantangan #1
    public function store(Request $request)
    {
        // $accept = $this->validateAccept($request);
        // if (!$accept) {
        //     return response('Not Acceptable!', 406);
        // }

        // if (!$this->validateContentType($request)) {
        //     return response('Unsupported Media Type', 415);
        // }
        // $userAda = \App\Models\User::find($request->user_id);
        // if (!$userAda) {
        //     return response()->json(['pesan_debug' => 'Lumen tidak bisa menemukan user dengan ID ini di database yang sedang dipakai'], 404);
        // }

        $input = $request->all();
        $validationRules = [
            'title' => 'required|min:5',
            'content' => 'required|min:10',
            'status' => 'required|in:draft,published',
            'user_id' => 'required|exists:users,id'
        ];

        $validator = Validator::make($input, $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $post = Post::create($input);
        return response()->json($post, 200);
        return $this->buildResponse($post, $accept, 201);
    }

    // PUT /post/{id} (Update) -> Tantangan #2 & #3
    public function update(Request $request, $id)
    {
        // $accept = $this->validateAccept($request);
        // if (!$accept) {
        //     return response('Not Acceptable!', 406);
        // }

        // if (!$this->validateContentType($request)) {
        //     return response('Unsupported Media Type', 415);
        // }

        // $post = Post::find($id);
        // if (!$post) {
        //     return $this->buildResponse(['message' => 'Post not found'], $accept, 404);
        // }

        $input = $request->all();

        $post = Post::find($id);

        if (!$post) {
            abort(404);
        }

        // validation
        $validationRules = [
            'title' => 'required|min:5',
            'content' => 'required|min:10',
            'status' => 'required|in:draft,published',
            'user_id' => 'required|exists:users,id'
        ];

        $validator = Validator::make($input, $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        // validation end

        $post->fill($input);
        $post->save();

        return response()->json($post, 200);

        $post->update($request->all());
        return $this->buildResponse($post, $accept);
    }

    // DELETE /post/{id} (Delete) -> Tantangan #2
    public function destroy(Request $request, $id)
    {
        $accept = $this->validateAccept($request);
        if (!$accept) {
            return response('Not Acceptable!', 406);
        }

        $post = Post::find($id);
        if (!$post) {
            abort(404);
        }

        $post->delete();
        return $this->buildResponse(['message' => 'Post deleted successfully'], $accept, 200);
    }
}
