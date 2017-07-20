<?php

class LibraryController extends Controller {

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('index', 'reserve'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionIndex() {

        $response = array();
        if ((Yii::app()->request->isPostRequest) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            

            $title = Yii::app()->request->getPost('title');
            $title = (!empty($title)) ? $title : NULL;

            $author = Yii::app()->request->getPost('author');
            $author = (!empty($author)) ? $author : NULL;

            $tag = Yii::app()->request->getPost('genre');
            $tag = (!empty($tag)) ? $tag : NULL;

            $page_no = Yii::app()->request->getPost('page_number');
            $page_no = (!empty($page_no)) ? $page_no : 1;

            $page_size = Yii::app()->request->getPost('page_size');
            $page_size = (!empty($page_size)) ? $page_size : 10;

            if (Yii::app()->user->user_secret === $user_secret) {
                $school_id = Yii::app()->user->schoolId;
                $books = new Books;
                $books = $books->getBookDetails($school_id, $title, $author, $tag, $page_no, $page_size);

                if (!$books) {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "BOOKS_NOT_FOUND";
                } else {

                    $response['data']['books'] = $books;

                    $books_cnt = new Books;
                    $response['data']['total'] = $books_cnt->getBookDetails($school_id, $title, $author, $tag, $page_no, $page_size, true);

                    $has_next = false;
                    if ($response['data']['total'] > $page_no * $page_size) {
                        $has_next = true;
                    }

                    $response['data']['has_next'] = $has_next;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "BOOKS_FOUND";
                }
            } else {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied";
            }
        } else {

            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionReserve() {
        $response = array();
        if ((Yii::app()->request->isPostRequest) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            

            $book_id = Yii::app()->request->getPost('book_id');

            if (empty($book_id)) {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
                echo CJSON::encode($response);
                Yii::app()->end();
            }

            if (Yii::app()->user->user_secret === $user_secret) {
                $school_id = Yii::app()->user->schoolId;
                $book = new Books;
                $book = $book->reserveBook($book_id);
                
                $response['status']['books'] = $book;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "BOOK_RESERVED";
                
            } else {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied";
            }
        } else {

            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

}
