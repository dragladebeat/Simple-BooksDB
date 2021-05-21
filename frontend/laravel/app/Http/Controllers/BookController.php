<?php

namespace App\Http\Controllers;

use App\Helpers\EncryptionHelper;
use App\Helpers\GuzzleHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->session()->has('auth')) {
            return redirect('/login');
        }

        $client = GuzzleHelper::get();

        $data_response['data'] = array();
        try {
            $response = $client->request('GET', env('API_URL') . 'books');

            $response_body = json_decode($response->getBody());
            switch ($response->getStatusCode()) {
                case 200:
                    $data_response['data'] = $response_body;
                    break;
                case 401:
                    $request->session()->flush();
                    return redirect('/login')->withError($response->getStatusCode() . ': ' . $response_body->error->message);
                default:
                    $data_response['error'] = $response->getStatusCode() . ': ' . $response_body->error->message;
            }
        } catch (Exception $e) {
            $data_response['error'] = '500' . ': ' . $e->getMessage();
        }

        return view('main.books.index', ['data' => $data_response]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (!$request->session()->has('auth')) {
            return redirect('/login');
        }

        $client = GuzzleHelper::get();

        $data_response['authors'] = array();
        try {
            $response = $client->request('GET', env('API_URL') . 'authors');

            $response_body = json_decode($response->getBody());
            switch ($response->getStatusCode()) {
                case 200:
                    $data_response['authors'] = $response_body;
                    break;
                case 401:
                    $request->session()->flush();
                    return redirect('/login')->withError($response->getStatusCode() . ': ' . $response_body->error->message);
                default:
                    $data_response['error'] = $response->getStatusCode() . ': ' . $response_body->error->message;
            }
        } catch (Exception $e) {
            $data_response['error'] = '500' . ': ' . $e->getMessage();
        }

        return view('main.books.create', ['data' => $data_response]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->session()->has('auth')) {
            return redirect('/login');
        }

        $client = GuzzleHelper::get();

        $post_request = [
            'cover' => !empty($request->file('cover')) ? base64_encode(file_get_contents($request->file('cover')->path())) : null,
            'title' => $request->input('title'),
            'summary' => $request->input('summary'),
            'author_id' => $request->input('author'),
        ];

        $encrypted = json_encode($post_request);
        $encryption_helper = new EncryptionHelper(session('auth')->access_token);

        $post_request = [
            'payload' => $encryption_helper->encrypt($encrypted)
        ];

        $data_response['data'] = array();
        try {
            $response = $client->request('POST', env('API_URL') . 'books', [
                'json' => $post_request
            ]);

            if (!empty(json_decode($response->getBody())->payload)) {
                $response_body = json_decode($encryption_helper->decrypt(json_decode($response->getBody())->payload));
            } else {
                $response_body = json_decode($response->getBody());
            }
            switch ($response->getStatusCode()) {
                case 200:
                    return redirect('/books')->with('success', 'Book ' . $response_body->title . ' has been created');
                    break;
                case 401:
                    $request->session()->flush();
                    return redirect('/login')->withError($response->getStatusCode() . ': ' . $response_body->error->message);
                default:
                    return redirect()->back()->withInput($request->input())->withError($response->getStatusCode() . ': ' . $response_body->error->message);
            }
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->input())->withError('500' . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        if (!$request->session()->has('auth')) {
            return redirect('/login');
        }
        return view('main.books.show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        if (!$request->session()->has('auth')) {
            return redirect('/login');
        }

        $client = GuzzleHelper::get();

        // Retrieve authors data
        $data_response['authors'] = array();
        try {
            $response = $client->request('GET', env('API_URL') . 'authors');

            $response_body = json_decode($response->getBody());;
            switch ($response->getStatusCode()) {
                case 200:
                    $data_response['authors'] = $response_body;
                    break;
                case 401:
                    $request->session()->flush();
                    return redirect('/login')->withError($response->getStatusCode() . ': ' . $response_body->error->message);
                default:
                    $data_response['error'] = $response->getStatusCode() . ': ' . $response_body->error->message;
            }
        } catch (Exception $e) {
            $data_response['error'] = '500' . ': ' . $e->getMessage();
        }

        // Retrieve latest book data
        $data_response['book'] = array();
        try {
            $response = $client->request('GET', env('API_URL') . 'books' . '/' . $id);

            $response_body = json_decode($response->getBody());
            switch ($response->getStatusCode()) {
                case 200:
                    $data_response['book'] = $response_body;
                    break;
                case 401:
                    $request->session()->flush();
                    return redirect('/login')->withError($response->getStatusCode() . ': ' . $response_body->error->message);
                default:
                    $data_response['error'] = $response->getStatusCode() . ': ' . $response_body->error->message;
            }
        } catch (Exception $e) {
            $data_response['error'] = '500' . ': ' . $e->getMessage();
        }

        return view('main.books.edit', ['data' => $data_response]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!$request->session()->has('auth')) {
            return redirect('/login');
        }

        $client = GuzzleHelper::get();

        $post_request = [
            'cover' => !empty($request->file('cover')) ? base64_encode(file_get_contents($request->file('cover')->path())) : null,
            'title' => $request->input('title'),
            'summary' => $request->input('summary'),
            'author_id' => $request->input('author'),
        ];



        $encrypted = json_encode($post_request);
        $encryption_helper = new EncryptionHelper(session('auth')->access_token);

        $post_request = [
            'payload' => $encryption_helper->encrypt($encrypted)
        ];

        $data_response['data'] = array();
        try {
            $response = $client->request('PUT', env('API_URL') . 'books' . '/' . $id, [
                'json' => $post_request
            ]);

            if (!empty(json_decode($response->getBody())->payload)) {
                $response_body = json_decode($encryption_helper->decrypt(json_decode($response->getBody())->payload));
            } else {
                $response_body = json_decode($response->getBody());
            }
            
            switch ($response->getStatusCode()) {
                case 200:
                    return redirect('/books')->with('success', 'Book ' . $response_body->title . ' has been updated');
                    break;
                case 401:
                    $request->session()->flush();
                    return redirect('/login')->withError($response->getStatusCode() . ': ' . $response_body->error->message);
                default:
                    return redirect()->back()->withInput($request->input())->withError($response->getStatusCode() . ': ' . $response_body->error->message);
            }
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->input())->withError('500' . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        if (!$request->session()->has('auth')) {
            return redirect('/login');
        }

        $client = GuzzleHelper::get();

        $data_response['data'] = array();
        try {
            $response = $client->request('DELETE', env('API_URL') . 'books' . '/' . $id);

            $encryption_helper = new EncryptionHelper(session('auth')->access_token);
            if (!empty(json_decode($response->getBody())->payload)) {
                $response_body = json_decode($encryption_helper->decrypt(json_decode($response->getBody())->payload));
            } else {
                $response_body = json_decode($response->getBody());
            }
            switch ($response->getStatusCode()) {
                case 200:
                    return redirect('/books')->with('success', 'Book ' . $response_body->title . ' has been deleted');
                    break;
                case 401:
                    $request->session()->flush();
                    return redirect('/login')->withError($response->getStatusCode() . ': ' . $response_body->error->message);
                default:
                    return redirect('/books')->withError($response->getStatusCode() . ': ' . $response_body->error->message);
            }
        } catch (Exception $e) {
            return redirect('/books')->withError('500' . ': ' . $e->getMessage());
        }
    }
}
