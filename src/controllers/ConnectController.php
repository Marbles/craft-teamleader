<?php
namespace marbles\teamleader\controllers;

use Craft;
use craft\web\Controller;
use Exception;

use marbles\teamleader\TeamleaderApi;
use yii\web\HttpException;

/**
 * simple-forms - Settings controller
 */
class ConnectController extends Controller
{
    /**
     * Make sure the current has access.
     *
     * @param $id
     * @param $module
     * @throws HttpException
     */
    public function __construct($id, $module)
    {
        parent::__construct($id, $module);
    }

    /**
     * Redirect index.
     */
    public function actionIndex()
    {
        $token = '';
        try {
            $token = TeamleaderApi::$connection->getAccessToken();
        } catch (\Exception $e) {

        }

        return $this->renderTemplate('teamleader/connect/index', [
            'settings' => TeamleaderApi::$plugin->getSettings(),
            'token' => $token
        ]);
    }

    /**
     * Redirect index.
     */
    public function actionSetConnection()
    {
        TeamleaderApi::$connection->acquireAccessToken();

        $this->redirect('/admin/teamleader/connect');
    }
}
