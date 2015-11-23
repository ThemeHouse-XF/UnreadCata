<?php

class ThemeHouse_UnreadCat_Listener_FileHealthCheck
{

    public static function fileHealthCheck(XenForo_ControllerAdmin_Abstract $controller, array &$hashes)
    {
        $hashes = array_merge($hashes,
            array(
                'library/ThemeHouse/UnreadCat/AlertHandler/UnreadCategory.php' => '1bc7b3de0d55772c7935a77f53e1aaed',
                'library/ThemeHouse/UnreadCat/CronEntry/UnreadCategories.php' => 'f98c272c26bb47c35700f897c4603b3d',
                'library/ThemeHouse/UnreadCat/Extend/XenForo/ControllerPublic/Category.php' => 'c7641a37c1898279e14db5aaa60c6520',
                'library/ThemeHouse/UnreadCat/Extend/XenForo/DataWriter/DiscussionMessage/Post.php' => '7256598fbd8d93d8ecfb8dee8d298abe',
                'library/ThemeHouse/UnreadCat/Extend/XenForo/Model/Alert.php' => 'ab9173d7a6aaede7ec08134e99c8d0c9',
                'library/ThemeHouse/UnreadCat/Extend/XenForo/Model/Category.php' => '814054c3ef91d581690b182da01715bc',
                'library/ThemeHouse/UnreadCat/Extend/XenForo/Model/User.php' => 'b2ac3f78f799e2a56ea290d6ae0afd87',
                'library/ThemeHouse/UnreadCat/Install/Controller.php' => 'fe358e9f1be5ca762cd0574a950af129',
                'library/ThemeHouse/UnreadCat/Listener/LoadClass.php' => '59175f3e97ec1ac4fac785243b333ebd',
                'library/ThemeHouse/UnreadCat/Listener/TemplateCreate.php' => '1a19e73b465f0c91954345cf917706ee',
                'library/ThemeHouse/UnreadCat/Option/CategoryChooser.php' => '3abb22f28cc42c066b81cffdb61950d3',
                'library/ThemeHouse/Install.php' => '18f1441e00e3742460174ab197bec0b7',
                'library/ThemeHouse/Install/20151109.php' => '2e3f16d685652ea2fa82ba11b69204f4',
                'library/ThemeHouse/Deferred.php' => 'ebab3e432fe2f42520de0e36f7f45d88',
                'library/ThemeHouse/Deferred/20150106.php' => 'a311d9aa6f9a0412eeba878417ba7ede',
                'library/ThemeHouse/Listener/ControllerPreDispatch.php' => 'fdebb2d5347398d3974a6f27eb11a3cd',
                'library/ThemeHouse/Listener/ControllerPreDispatch/20150911.php' => 'f2aadc0bd188ad127e363f417b4d23a9',
                'library/ThemeHouse/Listener/InitDependencies.php' => '8f59aaa8ffe56231c4aa47cf2c65f2b0',
                'library/ThemeHouse/Listener/InitDependencies/20150212.php' => 'f04c9dc8fa289895c06c1bcba5d27293',
                'library/ThemeHouse/Listener/LoadClass.php' => '5cad77e1862641ddc2dd693b1aa68a50',
                'library/ThemeHouse/Listener/LoadClass/20150518.php' => 'f4d0d30ba5e5dc51cda07141c39939e3',
                'library/ThemeHouse/Listener/Template.php' => '0aa5e8aabb255d39cf01d671f9df0091',
                'library/ThemeHouse/Listener/Template/20150106.php' => '8d42b3b2d856af9e33b69a2ce1034442',
                'library/ThemeHouse/Listener/TemplateCreate.php' => '6bdeb679af2ea41579efde3e41e65cc7',
                'library/ThemeHouse/Listener/TemplateCreate/20150106.php' => 'c253a7a2d3a893525acf6070e9afe0dd',
            ));
    }
}