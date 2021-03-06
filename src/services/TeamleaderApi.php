<?php
/**
 * Teamleader plugin for Craft CMS 3.x
 *
 * A connection between craft commerce and teamleader to have an easy flow of facturation
 *
 * @link      https://knapen.dev
 * @copyright Copyright (c) 2020 Kjell Knapen
 */

namespace marbles\teamleader\services;

use marbles\teamleader\TeamleaderApi as TeamleaderPlugin;

use Craft;
use craft\base\Component;
use craft\commerce\elements\Order;


/**
 * Teamleader Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Kjell Knapen
 * @package   Teamleader
 * @since     1.0.0
 */
class TeamleaderApi extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     TeamleaderApi::$plugin->teamleaderapi->exampleService()
     *
     * @return mixed
     */
    public function createInvoice(Order $order, $company)
    {
        $settings = TeamleaderPlugin::$plugin->getSettings();
        $lineItems = [];

        foreach ($order->lineItems as $key => $value) {
            $lineItems[] = [
              "quantity"=> $value->qty,
              "description"=> $value->snapshot['title'],
              "unit_price"=> [
                "amount"=> $value->price,
                "currency"=> "EUR",
                "tax"=> "excluding"
              ],
              "tax_rate_id"=> $settings->taxRateId,
          ];
        }


        $invoice = [
              "invoicee"=> [
                "customer"=> [
                  "type"=> "company",
                  "id"=> $company->id
                ]
              ],
              "department_id"=> $settings->departmentId,
              "payment_term"=> [
                "type"=> "cash"
              ],
              "grouped_lines"=> [
                [
                  "line_items"=> $lineItems
                ]
              ],
          ];

        $result = TeamleaderPlugin::$client->invoice($invoice)->save();

        $result->book();
        $result->registerPayment($order->totalPrice);

        return $result;
    }

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     TeamleaderApi::$plugin->teamleaderapi->createCompany()
     *
     * @return mixed
     */
    public function createCompany(Order $order)
    {
        $client = TeamleaderPlugin::$client;
        $billing = $order->billingAddress;
        $vat = $billing->businessTaxId;

        $filter = new \Teamleader\Actions\Attributes\Filter([
            'email'  => [
               'type' => 'primary',
               'email' => $order->email,
            ],
        ]);

        $company = $client->company()->getAll($filter);

        $vatCheck = $this->checkVat($vat);

        if (!$vatCheck) {
            $vat = '';
        }

        if (empty($company)) {
            $companyData = [
                'name' => $billing->firstName . ' ' . $billing->lastName,
                'business_type' => NULL,
                'national_identification_number' => NULL,
                'emails' =>
                   [
                       0 =>
                           [
                               'type' => 'primary',
                               'email' => $order->email,
                           ],
                   ],
                'addresses' =>
                   [
                       [
                           'type' => 'primary',
                           'address' => [
                               'line_1' => $billing->address1 . ' ' . $billing->address2,
                               'postal_code' => $billing->zipCode,
                               'city' => $billing->city,
                               'country' => 'BE',
                           ]
                       ],
                   ],
            ];

            if (!empty($vat)) {
                $companyData['name'] = $billing->businessName;
                $companyData['vat_number'] = $vat;
            }

            $company = $client->company($companyData)->save();
        } else {
            $company = $company[0];
        }

        return $company;
    }

    protected function checkVat($vat) {

        $countryCode = preg_replace('/[^A-Za-z]/', '', $vat);
        $vatNo = preg_replace('/[^0-9]/', '', $vat);
        try {
            $soap = new \SoapClient("http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl");
            $check = $soap->checkVat(['countryCode' => $countryCode, 'vatNumber' => $vatNo]);

            return (bool)$check->valid;
        } catch (\Exception $e) {
            return false;
        }
    }
}
