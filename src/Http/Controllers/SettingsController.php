<?php

	namespace DDM\CookieByte\Http\Controllers;

	use DDM\CookieByte\Configuration\CookieByteConfig;
	use DDM\CookieByte\CookieByte;
	use Illuminate\Http\Request;
use Statamic\Facades\Site;
use Statamic\Facades\User;
	use Statamic\Http\Controllers\CP\CpController;

	/**
	 * Class SettingsController
	 * @package DDM\CookieByte\Http\Controllers
	 * @author  DDM Studio
	 */
	class SettingsController extends CpController {

		public function __construct(Request $request) {
			parent::__construct($request);
		}

		public function index() {
			// No access if the user doesn't have the right permissions to show them
			abort_unless(User::current()->hasPermission('super') ||
				User::current()->hasPermission(CookieByte::PERMISSION_GENERAL_KEY), 403);

			$config = $this->getConfig();

			return view(CookieByte::getNamespacedKey('settings'), [
				'title' => CookieByte::getCpTranslation('title'),
				'action' => cp_route(CookieByte::ROUTE_SETTINGS_INDEX),
				'blueprint' => $config->blueprint()->toPublishArray(),
				'values' => $config->values(),
				'meta' => $config->fields()->meta()
			]);
		}

		public function update(Request $request) {
			// No access if the user doesn't have the right permissions to edit them
			abort_unless(User::current()->hasPermission('super') ||
				User::current()->hasPermission(CookieByte::PERMISSION_GENERAL_KEY), 403);

			$config = $this->getConfig();

			$values = $config->validatedValues($request);

			$this->config->setValues($values)->save();
		}

		public function getConfig() {
			return new CookieByteConfig(Site::selected()->locale());
		}

	}