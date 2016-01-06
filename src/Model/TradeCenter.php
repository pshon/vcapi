<?php
namespace VCAPI\Model;

use VCAPI\Common\Model;
use VCAPI\Common\Request;

class TradeCenter extends Model
{
    public $instanceIdentifier = '';

    /**
     * TradeCenter constructor.
     * @param string $instanceIdentifier
     */
    public function __construct($instanceIdentifier = '')
    {
        $this->instanceIdentifier = $instanceIdentifier;
    }

    /**
     * @return mixed
     */
    public function getItemsGroups()
    {
        $result = Request::get('/exchanges/items_groups_categories.json', $this->instanceIdentifier);

        return $this->validateResponse($result, 'categories');
    }

    /**
     * @return mixed
     */
    public function getBusinessGroups()
    {
        $result = Request::get('/exchanges/items_groups_categories_business.json', $this->instanceIdentifier);

        return $this->validateResponse($result, 'categories');
    }

    /**
     * Get list of groups from which user has items to sale
     *
     * @return mixed
     */
    public function getAvailableItemGroups()
    {
        $result = Request::get('/exchanges/available_items_groups_categories.json', $this->instanceIdentifier);

        return $this->validateResponse($result, 'categories');
    }

    /**
     * @return mixed
     */
    public function getAvailableBusinessGroups()
    {
        $result = Request::get('/exchanges/available_categories_business.json', $this->instanceIdentifier);

        return $this->validateResponse($result, 'categories');
    }

    /**
     * @return mixed
     */
    public function getAvailableStock()
    {
        $result = Request::get('/stock_exchanges/available_stock.json', $this->instanceIdentifier);

        return $this->validateResponse($result, 'stocks');
    }

    /**
     * @return mixed
     */
    public function getAvailableCurrency()
    {
        $result = Request::get('/currency/available.json', $this->instanceIdentifier);

        return $this->validateResponse($result, 'items');
    }

    /**
     * Get list of items available for sale. Items are from user inventory and available for sale
     *
     * @param $category (example: "special")
     * @return mixed
     */
    public function getAvailableItems($category)
    {
        $result = Request::get('/exchanges/available_items/' . $category . '.json', $this->instanceIdentifier);

        return $this->validateResponse($result, 'items');
    }

    /**
     * @return mixed
     */
    public function getCurrencyLotsList()
    {
        $result = Request::get('/currency/index.json', $this->instanceIdentifier);

        return $this->validateResponse($result, 'exchanges');
    }

    /**
     * List of currency lots vg/vd
     *
     * @param int $type
     * @return mixed
     */
    public function getCurrencyLotsByType($type = 0)
    {
        $result = Request::get('/currency/lots/' . (!$type ? 'vd' : 'vg') . '.json', $this->instanceIdentifier);

        return $this->validateResponse($result, 'offers');
    }

    /**
     * List of lots
     *
     * @param $category
     * @param null $itemName
     * @return mixed
     */
    public function getLotsList($category, $itemName = null)
    {
        $result = Request::get(
            '/exchanges/lots_by_type_user/' . $category . '.json' . ($itemName !== null ? '?itemTypeName=' . $itemName : ''),
            $this->instanceIdentifier
        );

        return $this->validateResponse($result, 'exchanges');
    }

    /**
     * Get lots proposed by City Hall
     */
    public function getLotsListCityHall()
    {
        $result = Request::get('/city_item_proposals/sell_index.json', $this->instanceIdentifier);

        return $this->validateResponse($result, 'exchanges');
    }

    /**
     * @param $category
     * @return mixed
     */
    public function getItemTypesList($category)
    {
        $result = Request::get('/exchanges/by_type/' . $category . '.json', $this->instanceIdentifier);

        return $this->validateResponse($result, 'exchanges');
    }

    /**
     * @param string $currency (vdollars OR vgold)
     * @param $itemTypeId
     * @param string $sort (price or date)
     * @param string $direction (asc OR desc)
     * @return mixed
     */
    public function getLotsOffer($currency, $itemTypeId, $sort = 'price', $direction = 'asc')
    {
        $result = Request::get('/exchanges/lot_offers/' . $currency . '/' . $itemTypeId . '/' . $sort . '/' . $direction . '.json', $this->instanceIdentifier);

        return $this->validateResponse($result, 'offers');
    }

}