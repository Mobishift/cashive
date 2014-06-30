<?php

class UserController extends \BaseController {

    protected $layout = 'layouts.site';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $this->layout->content = View::make('user.sign_up');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $rules = array(
            'name'    => 'required|min:3',
            'email'     => 'required|email', 
            'password'     => 'required|alphaNum|min:3'
        );

        $validator = Validator::make(Input::all(), $rules);

        if ( $validator->fails() )
        {
            return Redirect::action('UserController@create')
                ->withInput( Input::except('password') )
                ->withErrors($validator->errors());
        } 
        else if ( User::where('email', '=', Input::get('email'))->first() )
        {
            return Redirect::action('UserController@create')
                ->withInput( Input::except('password') )
                ->withErrors('Duplicated email address.');            
        }
        else 
        {
            $userdata = array(
                'name'        => Input::get('name'),
                'email'       => Input::get('email'),
                'password'    => Hash::make(Input::get('password'))
            );
            
            $response = Cashive::request('POST', 'contributors', array(
                'email' => $userdata['email'],
                'realname' => $userdata['name'],
            ));
            if($response->is_success){
                $userdata['uid'] = $response->get_data()['uid'];
            }else{
                Log::error("User store failed, HTTP_CODE: {$response->http_code}, ERROR_RESPONSE: ".var_export($response->response, TRUE));
                return Redirect::action('UserController@create')
                    ->withInput( Input::except('password') )
                    ->withErrors($response->get_errmsg());    
            }
            
            $newUser = User::create($userdata);

            if ($newUser && $newUser->id > 0) 
            {
                return Redirect::to('admin');
            }
            else
            {
                return Redirect::action('UserController@create')
                    ->withInput( Input::except('password') )
                    ->withErrors('Invalid email or password');
            }
        }

    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $this->layout->content = View::make('user.edit')->with('user', Auth::user());
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function showSignIn()
    {
        $this->layout->content = View::make('user.sign_in');
    }

    public function doSignIn()
    {
        $rules = array(
            'email' => 'required|email', 
            'password' => 'required|alphaNum|min:3'
        );

        $validator = Validator::make(Input::all(), $rules);

        if ( $validator->fails() )
        {
            return Redirect::route('sign_in_path')
                ->withInput( Input::except('password') )
                ->withErrors($validator->errors());
        } 
        else 
        {
            $userdata = array(
                'email'        => Input::get('email'),
                'password'    => Input::get('password')
            );

            if (Auth::attempt($userdata)) 
            {
                return Redirect::to('/');
            }
            else
            {
                return Redirect::route('sign_in_path')
                    ->withInput( Input::except('password') )
                    ->withErrors('Invalid email or password');
            }
        }
    }

    public function getSignOut()
    {
        Auth::logout();
        return Redirect::route('sign_in_path')->withMessage('You are signed out.');
    }
}
