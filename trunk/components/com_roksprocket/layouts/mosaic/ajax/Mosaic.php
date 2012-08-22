<?php
/**
 * @version   $Id: Mosaic.php 54417 2012-07-18 22:22:53Z build $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokSprocketSiteLayoutAjaxModelMosaic extends RokSprocket_AbstractAjaxRenderingLayoutModel
{
	/**
	 * @param $params
	 * {
	 *  "page":1,
	 *  "moduleid": 86
	 * }
	 *
	 * @return RokCommon_Ajax_Result
	 */
	public function getPage($params)
	{
		$result = new RokCommon_Ajax_Result();
		try {
			$html = '';

			$container = RokCommon_Service::getContainer();

			/** @var $platformHelper RokSprocket_PlatformHelper */
			$platformHelper = $container->roksprocket_platformhelper;
			$module_params  = $platformHelper->getModuleParameters($params->moduleid);
			// add the layout classpath
			$layout_lib_path = $container['roksprocket.layouts.mosaic.library.paths'];
			foreach ($layout_lib_path as $lib_path) {
				RokCommon_ClassLoader::addPath($lib_path);
			}

			$container = RokCommon_Service::getContainer();
			/** @var $platformHelper RokSprocket_PlatformHelper */
			$platformHelper = $container->roksprocket_platformhelper;
			$items = $platformHelper->getFromCache(array('RokSprocket', 'getItemsWithParams'), array($params->moduleid, $module_params, true), $module_params, $params->moduleid);

			/** @var $layout RokSprocket_Layout_Mosaic */
			$layout = $container->getService('roksprocket.layout.mosaic');
			$layout->initialize($items, $module_params);
			$items = $layout->getItems();

			if (isset($params->filter) && $params->filter && $params->filter != 'all'){
				$filtered = new RokSprocket_ItemCollection();
				foreach($items as $item){
					$tags = $item->getParam('mosaic_item_tags', '');
					$tags = explode(",", $tags);

					foreach($tags as $i => $tag){
						$cleanName = trim($tag);
						$tags[$i] = str_replace(' ', '-', str_replace(array("'", '"'), '', $cleanName));
					}

					if (in_array($params->filter, $tags)) $filtered->addItem($item);
				}

				$items = $filtered;
			}

			$total_items = count($items);

			$provider_type = $module_params->get('provider', 'joomla');
			$sort_type     = $module_params->get($provider_type . '_sort', 'automatic');
			if ($sort_type == RokSprocket_ItemCollection::SORT_METHOD_RANDOM) {
				$items->sort($sort_type);
			}

			$limit = $module_params->get('display_limit', '∞');
			if ($limit != '∞' && (int)$limit > 0) {
				$items = $items->trim($limit);
			}
			$per_page     = $module_params->get('mosaic_items_per_page', 1);
			$offset       = ($params->page - 1) * $per_page;
			if (isset($params->all) && $params->all) $items = $items->slice($offset, $total_items);
			else $items = $items->slice($offset, $per_page);
			$items        = $platformHelper->processItemsForEvents($items, $module_params);

			$more = (isset($params->all) && $params->all) ? false : $params->page < ceil($total_items / $per_page);

			$themecontext = $layout->getThemeContent();
			ob_start();
			$index = 0;
			foreach ($items as $item) {
				echo $themecontext->load('item.php', array('item' => $item, 'parameters' => $module_params, 'index'	  => $index));
				$index++;
			}
			$html .= ob_get_clean();

			$result->setPayload(array(
			                         'page'     => $params->page,
			                         'more'     => $more,
		                             'behavior' => $params->behavior,
			                         'html'     => $html
			                    ));
		} catch (Exception $e) {
			throw $e;
		}
		return $result;
	}
}
