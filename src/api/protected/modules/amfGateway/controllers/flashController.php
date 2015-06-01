<?php
/*
* @author Vassilis Papapanagiotou <bpapapana@gmail.com>, Vassilis Triglianos <triglian@gmail.com>
* @link https://github.com/bsquaredlabs/YiiAmfGateway
* @copyright Copyright &copy; 2011 BSquaredLabs(http://bsquaredlabs.com),Vassilis Papapanagiotou & Vassilis Triglianos
* @license http://bsquaredlabs.com/licenses/YiiAmfGatewayLicense
*/
class FlashController extends AmfController
{
	public function actionIndex() {
          	#$productionMode = Yii::app()->getModule('amfGateway')->production;
		$this->setProductionMode(FALSE);
		$servicesFolder = Yii::getPathOfAlias("application.services.amf");
		$this->handle($servicesFolder);
		return $servicesFolder;
	}	
}