<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use SimpleXMLElement;

class PostsController extends Controller
{
    // Validasi Accept Header (hanya application/json atau application/xml)
    private function validateAccept(Request $request)
    {
        $accept = $request->header('Accept');
        if ($accept !== 'application/json' && $accept !== 'application/xml') {
            return false;
        }
        return $accept;
    }

    // Validasi Content-Type Header (hanya application/json)
    private function validateContentType(Request $request)
    {
        return $request->header('Content-Type') === 'application/json';
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

        $posts = Post::all();
        return $this->buildResponse($posts, $accept);
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
            return $this->buildResponse(['message' => 'Post not found'], $accept, 404);
        }

        return $this->buildResponse($post, $accept);
    }

    // POST /posts (Create) -> Tantangan #1
    public function store(Request $request)
    {
        $accept = $this->validateAccept($request);
        if (!$accept) {
            return response('Not Acceptable!', 406);
        }

        if (!$this->validateContentType($request)) {
            return response('Unsupported Media Type', 415);
        }

        $post = Post::create($request->all());
        return $this->buildResponse($post, $accept, 201);
    }

    // PUT /post/{id} (Update) -> Tantangan #2 & #3
    public function update(Request $request, $id)
    {
        $accept = $this->validateAccept($request);
        if (!$accept) {
            return response('Not Acceptable!', 406);
        }

        if (!$this->validateContentType($request)) {
            return response('Unsupported Media Type', 415);
        }

        $post = Post::find($id);
        if (!$post) {
            return $this->buildResponse(['message' => 'Post not found'], $accept, 404);
        }

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
            return $this->buildResponse(['message' => 'Post not found'], $accept, 404);
        }

        $post->delete();
        return $this->buildResponse(['message' => 'Post deleted successfully'], $accept);
    }
}