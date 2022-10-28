<?php
/**
 * @package     wt_jshopping_b24_pro_cron
 *
 * @copyright   (C) 2022 Sergey Tolkachyov, https://web-tolk.ru
 * @license     GNU General Public License version 3
 */
namespace Joomla\Plugin\Task\Wt_jshopping_b24_pro_cron\Extension;
// Restrict direct access
defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Component\Scheduler\Administrator\Task\Status as TaskStatus;
use Joomla\Component\Scheduler\Administrator\Traits\TaskPluginTrait;
use Joomla\Event\SubscriberInterface;
use Joomla\Registry\Registry;
use Joomla\Plugin\System\Wt_jshopping_b24_pro\Library\CRest;


/**
 * Task plugin with routines to make HTTP requests.
 * At the moment, offers a single routine for GET requests.
 *
 * @since  4.1.0
 */
class Wt_jshopping_b24_pro_cron extends CMSPlugin implements SubscriberInterface
{
	use TaskPluginTrait;

	/**
	 * @var string[]
	 * @since 4.1.0
	 */
	protected const TASKS_MAP = [
		'plg_task_update_jshopping_data_from_bitrix24_task_update' => [
			'langConstPrefix' => 'PLG_TASK_WT_JSHOPPING_B24_PRO_CRON_UPDATE_JSHOPPING_DATA_FROM_BITRIX24',
			'form'            => 'update_jshopping_data_from_bitrix24',
			'method'          => 'update_jshopping_data_from_bitrix24',
		],
	];

	/**
	 * @var boolean
	 * @since 4.1.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * @inheritDoc
	 *
	 * @return string[]
	 *
	 * @since 4.1.0
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onTaskOptionsList'    => 'advertiseRoutines',
			'onExecuteTask'        => 'standardRoutineHandler',
			'onContentPrepareForm' => 'enhanceTaskItemForm',
		];
	}

	/**
	 * Метод для обновления цен и остатков товаров JoomShopping из Битрикс 24
	 *
	 * @param   ExecuteTaskEvent  $event  The onExecuteTask event
	 *
	 * @return integer  The exit code
	 *
	 * @since 4.1.0
	 * @throws Exception
	 */
	protected function update_jshopping_data_from_bitrix24(ExecuteTaskEvent $event): int
	{
		$id     = $event->getTaskId();
		$params = $event->getArgument('params');
		if (!PluginHelper::isEnabled('system','wt_jshopping_b24_pro'))
		{
			$this->logTask(Text::_('WT JoomShopping Bitrix 24 PRO is not enabled'), 'warning');

			return TaskStatus::NO_RUN;
		}

		$wt_jshopping_b24_pro = PluginHelper::getPlugin('system','wt_jshopping_b24_pro');
		$wt_jshopping_b24_pro_params = json_decode($wt_jshopping_b24_pro->params);
		$crm_host        = $wt_jshopping_b24_pro_params->crm_host;
		$webhook_secret  = $wt_jshopping_b24_pro_params->crm_webhook_secret;
		$crm_assigned_id = $wt_jshopping_b24_pro_params->crm_assigned;
		if (!empty($crm_host) && !empty($webhook_secret) && !empty($crm_assigned_id))
		{
			if(!defined('C_REST_WEB_HOOK_URL')){
				define('C_REST_WEB_HOOK_URL', 'https://' . $crm_host . '/rest/' . $crm_assigned_id . '/' . $webhook_secret . '/');//url on creat Webhook
			}

		}
		else
		{

				$this->logTask("Bitrix 24 webhook data are not exists in WT JoomShopping Bitrix 24 PRO plugin", 'error');
				return TaskStatus::KNOCKOUT;

		}

		$b24_jshopping_relationships = $this->getJshoppingBitrix24ProductRelationships();

		if(is_countable($b24_jshopping_relationships) && count($b24_jshopping_relationships) > 0){
			$db    = Factory::getContainer()->get('DatabaseDriver');
			$params->default_bitrix24_store_iblock_id = $wt_jshopping_b24_pro_params->default_bitrix24_store_iblock_id;
			foreach ($b24_jshopping_relationships as $relationship){

				$b24_product_data = $this->getB24Product($relationship['bitrix24_product_id'], $params);

				if (count($b24_product_data) > 0)
				{

					$query = $db->getQuery(true);
					$query->update($db->quoteName('#__jshopping_products'));

					$message_new_product_price='';
					$message_new_product_quantity='';

					if (isset($b24_product_data['product_price']))
					{
						$query->set($db->quoteName("product_price") . " = " . floatval($b24_product_data['product_price']));
						$message_new_product_price = 'new product price is '.floatval($b24_product_data['product_price']).'. ';
					}

					if (isset($b24_product_data['product_quantity']))
					{
						$query->set($db->quoteName("product_quantity") . " = " . floatval($b24_product_data['product_quantity']));
						$message_new_product_quantity = 'new product price is '.floatval($b24_product_data['product_quantity']).'. ';
					}

					$query->where($db->quoteName("product_id") . " = " . $relationship['jshopping_product_id']);
					$result = $db->setQuery($query)->execute();
					$this->logTask('JoomShopping product id '.$relationship['jshopping_product_id'].' successfully updated from Bitrix 24. New data: '.$message_new_product_price.$message_new_product_quantity, 'INFO');

				}
				else
				{
					$this->logTask('There is no product data form Bitrix 24 for ID '.$relationship['bitrix24_product_id'], 'WARNING');
				}

			}
		} else {

			$this->logTask("There is no JoomShopping and Bitrix 24 products relationships exists in database", 'WARNING');
			return TaskStatus::KNOCKOUT;
		}

		return TaskStatus::OK;
	}

	/**
	 * Get product price and product quantity form Bitrix 24 by product id
	 *
	 * @param $b24_product_id string|int Bitrix 24 product id
	 *
	 * @return array Bitrix24 product price and product quantity array
	 * @since 3.0.0
	 */
	private function getB24Product($b24_product_id, $task_params): array
	{
		if (!empty($b24_product_id))
		{

			$resultBitrix24 = [];
			if (in_array('price',$task_params->update_product_data))
			{
				$resultBitrix24ProductPrice      = CRest::call("catalog.price.list", [
					'select' => [
						'price'
					],
					'filter' => [
						'productId' => $b24_product_id, // Фильтр по id Товара
					]
				]);
				$resultBitrix24['product_price'] = $resultBitrix24ProductPrice['result']['prices'][0]['price'];
			}

			if (in_array('quantity',$task_params->update_product_data))
			{
				$resultBitrix24ProductQuantity      = CRest::call("catalog.product.list", [
					'select' => [
						'id', 'iblockId', 'name', 'quantity', 'xmlId'
					],
					'filter' => [
						'id'       => $b24_product_id, // Фильтр по id Товара
						'iblockId' => $task_params->default_bitrix24_store_iblock_id
					]
				]);
				$resultBitrix24['product_quantity'] = $resultBitrix24ProductQuantity['result']['products'][0]['quantity'];
			}


			return $resultBitrix24;
		}

		return [];
	}

	/**
	 * Получаем список соответствий товаров JoomShopping и Битрикс 24
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	private function getJshoppingBitrix24ProductRelationships(): array
	{
		$db = Factory::getContainer()->get('DatabaseDriver');

		$query = "SELECT * FROM `#__wt_jshopping_bitrix24_pro_products_relationship`";
		$db->setQuery($query);
		return $db->loadAssocList();

	}
}
