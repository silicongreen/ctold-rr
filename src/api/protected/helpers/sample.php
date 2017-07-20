<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class encription_method
{

    public static $method = array("c", "p", "s", "m", "d");
    public static $operator = array("m", "p");
    public static $encoded_left = TRUE;
    public static $encoded_right = TRUE;
    public static $encoded_method = TRUE;
    public static $encoded_operator = TRUE;
    public static $encoded_send_id = TRUE;
    public static $check_service = false;
    public static $check_id = 259;

    /**
     * setCache
     *
     * Set cache of auth 
     *
     * @param (string) ($cache_name) name of the response cache
     * @param (string) ($response) encripted auth id
     * @return null
     */
    public static function setCache($cache_name, $response)
    {
        $cachefile = new CFileCache();
        $cachefile->cachePath = "protected/runtime/cache/auth/";
        if (!is_dir($cachefile->cachePath))
        {
            mkdir($cachefile->cachePath, 0777);
        }
        $cachefile->set($cache_name, $response, 31536000);
    }

    /**
     * getCache
     *
     * Get cache of auth 
     *
     * @param (string) ($cache_name) name of the cache
     * @return encripted id
     */
    public static function getCache($cache_name)
    {
        $cachefile = new CFileCache();
        $cachefile->cachePath = "protected/runtime/cache/auth/";
        if (!is_dir($cachefile->cachePath))
        {
            mkdir($cachefile->cachePath, 0777);
        }
        $response = $cachefile->get($cache_name);
        return $response;
    }

    /**
     * createUserToken
     *
     * create user tokens for un encripted id 
     *
     * @param (int) ($auth_id) id that have to encripted
     * @return auth id tokens as object which is use for depripted
     */
    public static function createUserToken($auth_id)
    {
        $leftstring = rand(1000, 1000000);
        $rightstring = rand(100, 100000);



        $leftvalue = strlen($leftstring);
        $rightvalue = strlen($rightstring);




        $method_main = self::$method[array_rand(self::$method)];



        $operator_main = self::$operator[array_rand(self::$operator)];

        $encoded_method = self::createMethodEncoded($method_main);




        $encripted_auth_id = self::createEncriptedUserID($encoded_method, $operator_main, $auth_id, $leftvalue, $rightvalue);

        $auth_id_created = $leftstring . $encripted_auth_id . $rightstring;



        $return1 = $return = array("left" => $leftvalue, "right" => $rightvalue, "method" => $encoded_method, "operator" => $operator_main, "auth_id_token" => $auth_id_created);

        $cache_name = "USER_TOKEN_CACHE";
        $response[$auth_id][] = $auth_id_created;
        self::setCache($cache_name, $response);

        if (self::$encoded_right)
        {
            $return['right'] = base64_encode($return['right']);
        }

        if (self::$encoded_left)
        {
            $return['left'] = base64_encode($return['left']);
        }

        if (self::$encoded_send_id)
        {
            $return['auth_id_token'] = base64_encode($return['auth_id_token']);
        }

        if (self::$encoded_method)
        {
            $return['method'] = base64_encode($return['method']);
        }

        if (self::$encoded_operator)
        {
            $return['operator'] = base64_encode($return['operator']);
        }

        return (object) $return;
    }

    /**
     * createEncriptedUserID
     *
     * create Encripted UserID
     * @param (string) ($left) left token
     * @param (string) ($right) right token
     * @param (string) ($method) method to use
     * @param (string) ($operator) operator to use
     * @param (string) ($auth_id) unencripted id
     * @return string encoded method
     */
    public static function createEncriptedUserID($method, $operator, $auth_id, $left, $right)
    {
        $auth_id_decrepted = 0;
        if (strpos($method, self::$method[0]) !== FALSE)
        {
            $concated_value = $left . "" . $right;
            $value = (int) $concated_value;
            if ($operator == self::$operator[0])
            {
                $auth_id_decrepted = $auth_id + $value;
            } else if ($operator == self::$operator[1])
            {
                $auth_id_decrepted = $auth_id - $value;
            }
        } else if (strpos($method, self::$method[1]) !== FALSE)
        {
            $value = $left + $right;
            if ($operator == self::$operator[0])
            {
                $auth_id_decrepted = $auth_id + $value;
            } else if ($operator == self::$operator[1])
            {
                $auth_id_decrepted = $auth_id - $value;
            }
        } else if (strpos($method, self::$method[2]) !== FALSE)
        {

            $value = $left - $right;


            if ($operator == self::$operator[0])
            {
                $auth_id_decrepted = $auth_id + $value;
            } else if ($operator == self::$operator[1])
            {
                $auth_id_decrepted = $auth_id - $value;
            }
        } else if (strpos($method, self::$method[3]) !== FALSE)
        {

            $value = $left * $right;

            if ($operator == self::$operator[0])
            {
                $auth_id_decrepted = $auth_id + $value;
            } else if ($operator == self::$operator[1])
            {
                $auth_id_decrepted = $auth_id - $value;
            }
        } else if (strpos($method, self::$method[4]) !== FALSE)
        {

            $value = ceil($left / $right);

            if ($operator == self::$operator[0])
            {
                $auth_id_decrepted = $auth_id + $value;
            } else if ($operator == self::$operator[1])
            {
                $auth_id_decrepted = $auth_id - $value;
            }
        }
        return $auth_id_decrepted;
    }

    /**
     * createMethodEncoded
     *
     * create a random string
     *
     * @param (string) ($method_main) method to concat with randrom string
     * @return string encoded method
     */
    public static function createMethodEncoded($method_main)
    {
        $length = rand(2, 5);
        $characters = 'abefghijklnoqrtuvwxyz';
        $charactersLength = strlen($characters);
        $encoded_method = '';
        $randomString1 = '';
        for ($i = 0; $i < $length; $i++)
        {
            $randomString1 .= $characters[rand(0, $charactersLength - 1)];
        }

        $encoded_method.= $randomString1 . $method_main;


        $randomString2 = '';
        for ($i = 0; $i < $length; $i++)
        {
            $randomString2 .= $characters[rand(0, $charactersLength - 1)];
        }


        $encoded_method.=$randomString2;

        return $encoded_method;
    }

    /**
     * authorizeUserCheck
     *
     * decriped tokens and check user token is authorize
     *
     * @param (string) ($left) left token
     * @param (string) ($right) right token
     * @param (string) ($method) method to use
     * @param (string) ($operator) operator to use
     * @param (string) ($auth_id) encriptede id
     * @param (string) ($session_id) for checking cache
     * @return bool
     */
    public static function authorizeUserCheck($left, $right, $method, $operator, $auth_id, $session_id)
    {

        if (self::$encoded_send_id)
        {
            $auth_id = base64_decode($auth_id);
        }

        $cache_name = "USER_TOKEN_CACHE";
        $response = self::getCache($cache_name);


        if ($response !== FALSE)
        {

            if (isset($response[$session_id]))
            {

                if (in_array($auth_id, $response[$session_id]))
                {

                    return FALSE;
                }
            }
        }


        if (self::$encoded_right)
        {
            $right = base64_decode($right);
        }

        if (self::$encoded_left)
        {
            $left = base64_decode($left);
        }

        if (self::$encoded_method)
        {
            $method = base64_decode($method);
        }

        if (self::$encoded_operator)
        {
            $operator = base64_decode($operator);
        }

        $left_position = $left - 1;
        $auth_id_without_left = substr($auth_id, $left);



        $right_position = strlen($auth_id_without_left) - $right;
        $auth_id = substr($auth_id_without_left, 0, $right_position);



        if (strpos($method, self::$method[0]) !== FALSE)
        {
            $concated_value = $left . "" . $right;
            $value = (int) $concated_value;
            if ($operator == self::$operator[0])
            {
                $auth_id_decrepted = $auth_id - $value;
            } else if ($operator == self::$operator[1])
            {
                $auth_id_decrepted = $auth_id + $value;
            }
        } else if (strpos($method, self::$method[1]) !== FALSE)
        {
            $value = $left + $right;
            if ($operator == self::$operator[0])
            {
                $auth_id_decrepted = $auth_id - $value;
            } else if ($operator == self::$operator[1])
            {
                $auth_id_decrepted = $auth_id + $value;
            }
        } else if (strpos($method, self::$method[2]) !== FALSE)
        {

            $value = $left - $right;


            if ($operator == self::$operator[0])
            {
                $auth_id_decrepted = $auth_id - $value;
            } else if ($operator == self::$operator[1])
            {
                $auth_id_decrepted = $auth_id + $value;
            }
        } else if (strpos($method, self::$method[3]) !== FALSE)
        {

            $value = $left * $right;

            if ($operator == self::$operator[0])
            {
                $auth_id_decrepted = $auth_id - $value;
            } else if ($operator == self::$operator[1])
            {
                $auth_id_decrepted = $auth_id + $value;
            }
        } else if (strpos($method, self::$method[4]) !== FALSE)
        {

            $value = ceil($left / $right);

            if ($operator == self::$operator[0])
            {
                $auth_id_decrepted = $auth_id - $value;
            } else if ($operator == self::$operator[1])
            {
                $auth_id_decrepted = $auth_id + $value;
            }
        }


        if (isset($auth_id_decrepted) && $auth_id_decrepted == $session_id)
        {
            $response[$session_id][] = $auth_id;
            self::setCache($cache_name, $response);
            return TRUE;
        }
        return FALSE;
    }

}
