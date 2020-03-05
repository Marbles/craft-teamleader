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
class TeamleaderController extends Controller
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
        $this->renderTemplate('teamleader/index', [
            'products' => TeamleaderApi::$client->product()->get(),
            'departments' => TeamleaderApi::$client->department()->get(),
            'taxRates' => TeamleaderApi::$client->taxRate()->get(),
        ]);
    }

    /**
     * Show settings.
     */
    public function actionSettings()
    {
        $this->redirect('/admin/settings/plugins/teamleader');
    }
}
