<?php
use app\models\Users;
?>

<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
<!--        <div class="user-panel">-->
<!--            <div class="pull-left image">-->
<!--                <img src="--><?//= $directoryAsset ?><!--/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>-->
<!--            </div>-->
<!--            <div class="pull-left info">-->
<!--                <p>Alexander Pierce</p>-->
<!---->
<!--                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>-->
<!--            </div>-->
<!--        </div>-->

        <!-- search form -->
<!--        <form action="#" method="get" class="sidebar-form">-->
<!--            <div class="input-group">-->
<!--                <input type="text" name="q" class="form-control" placeholder="Search..."/>-->
<!--              <span class="input-group-btn">-->
<!--                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>-->
<!--                </button>-->
<!--              </span>-->
<!--            </div>-->
<!--        </form>-->
        <!-- /.search form -->

        <?php if(Yii::$app->user->identity->role == Users::ROLE_ADMIN)
                {
                   echo dmstr\widgets\Menu::widget(
                        [
                            'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                            'items' => [
                //                    ['label' => 'Меню конструктора', 'options' => ['class' => 'header']],
                                [
                                    'label' => 'Материалы',
                                    'icon' => 'file',
                                    'url' => ['/materials/all-materials','not_in_archive' => true],
//                                    'items' => [
////                                        ['label' => 'Актуальные материалы', 'icon' => 'file-text', 'url' => ['/materials/all-materials']],
////                                        ['label' => 'Удаленные материалы', 'icon' => 'trash', 'url' => ['/materials/all-materials','in_archive'=>\app\models\Materials::ARCHIVE]],
//                                    ],
                                ],
                                [
                                    'label' => 'Категории',
                                    'icon' => 'map-signs',
                                    'url' => '#',
                                    'items' => [
                                        ['label' => 'Актуальные', 'icon' => 'toggle-on', 'url' => ['/tree/all-categories','in_archive' => false],],
                                        ['label' => 'Архивные', 'icon' => 'toggle-off', 'url' => ['/tree/all-categories','in_archive' => true],],
                                    ],
                                ],
                                ['label' => 'Теги', 'icon' => 'file-text', 'url' => ['/administrator/all-tags']],
                                ['label' => 'Проекты', 'icon' => 'file-text',
                                    'items' => [
                                        ['label' => 'Актуальные', 'icon' => 'toggle-on', 'url' => ['/projects/all-projects','outdated' => false],],
                                        ['label' => 'Архивные', 'icon' => 'toggle-off', 'url' => ['/projects/all-projects','outdated' => true],],
                                    ]
                                ],
                                ['label' => 'План работы', 'icon' => 'file-text',
                                    'url' => ['/plan/all-work-plan']
                                ],
                                ['label' => 'Вебинары', 'icon' => 'youtube-play',
                                    'url' => ['/webinars/all-webinars']
                                ],
                                ['label' => 'Пользователи', 'icon' => 'user', 'url' => ['/user/all-users','not_user' => true]],
                                ['label' => 'Группы пользователей', 'icon' => 'users', 'url' => ['/user/all-user-groups']],
                                ['label' => 'Подписчики', 'icon' => 'users', 'url' => ['/administrator/all-subscribers']],


                //                    ['label' => 'Формы', 'icon' => 'file-text', 'url' => ['/administrator/all-forms']],
                //                    ['label' => 'Статистика', 'icon' => 'line-chart', 'url' => ['/administrator/statistics']],
                //                    ['label' => 'Пользователи', 'icon' => 'users', 'url' => ['/administrator/all-users']],
                //                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                //                    [
                //                        'label' => 'Some tools',
                //                        'icon' => 'share',
                //                        'url' => '#',
                //                        'items' => [
                //                            ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii'],],
                //                            ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug'],],
                //                            [
                //                                'label' => 'Level One',
                //                                'icon' => 'circle-o',
                //                                'url' => '#',
                //                                'items' => [
                //                                    ['label' => 'Level Two', 'icon' => 'circle-o', 'url' => '#',],
                //                                    [
                //                                        'label' => 'Level Two',
                //                                        'icon' => 'circle-o',
                //                                        'url' => '#',
                //                                        'items' => [
                //                                            ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
                //                                            ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
                //                                        ],
                //                                    ],
                //                                ],
                //                            ],
                //                        ],
                //                    ],
                            ],
                        ]
                    );
                }
                else if(Yii::$app->user->identity->role == Users::ROLE_METHODIST)
                {
                    echo dmstr\widgets\Menu::widget(
                        [
                            'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                            'items' => [
                                //                    ['label' => 'Меню конструктора', 'options' => ['class' => 'header']],
                                [
                                    'label' => 'Материалы',
                                    'icon' => 'file',
                                    'url' => ['/materials/all-materials','not_in_archive' => true],
//                                    'items' => [
////                                        ['label' => 'Актуальные материалы', 'icon' => 'file-text', 'url' => ['/materials/all-materials']],
////                                        ['label' => 'Удаленные материалы', 'icon' => 'trash', 'url' => ['/materials/all-materials','in_archive'=>\app\models\Materials::ARCHIVE],],
//                                    ],
                                ],
                                ['label' => 'Профиль', 'icon' => 'user', 'url' => ['/user/profile']],
                            ],
                        ]
                    );
                }
                else if(Yii::$app->user->identity->role == Users::ROLE_SENIOR_METHODIST)
                {
                    echo dmstr\widgets\Menu::widget(
                        [
                            'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                            'items' => [
                                //                    ['label' => 'Меню конструктора', 'options' => ['class' => 'header']],
                                [
                                    'label' => 'Материалы',
                                    'icon' => 'file',
                                    'url' => ['/materials/all-materials','not_in_archive' => true],
//                                    'items' => [
//                                        ['label' => 'Актуальные материалы', 'icon' => 'file-text', 'url' => ['/materials/all-materials'],],
//                                    ],
                                ],
                                ['label' => 'План работы', 'icon' => 'file-text',
                                    'url' => ['/plan/all-work-plan']
                                ],
                                ['label' => 'Профиль', 'icon' => 'user', 'url' => ['/user/profile']],
                            ],
                        ]
                    );
                }
                else if(Yii::$app->user->identity->role == Users::ROLE_MODERATOR)
                {
                    echo dmstr\widgets\Menu::widget(
                        [
                            'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                            'items' => [
                                //                    ['label' => 'Меню конструктора', 'options' => ['class' => 'header']],
                                [
                                    'label' => 'Материалы',
                                    'icon' => 'file',
                                    'url' => ['/materials/all-materials','not_in_archive' => true],
//                                    'items' => [
////                                        ['label' => 'Актуальные материалы', 'icon' => 'file-text', 'url' => ['/materials/all-materials'],],
////                                        ['label' => 'Удаленные материалы', 'icon' => 'trash', 'url' => ['/materials/all-materials','in_archive'=>\app\models\Materials::ARCHIVE],],
//                                    ],
                                ],
                                [
                                    'label' => 'Категории',
                                    'icon' => 'map-signs',
                                    'url' => '#',
                                    'items' => [
                                        ['label' => 'Актуальные', 'icon' => 'toggle-on', 'url' => ['/tree/all-categories','in_archive' => false],],
                                        ['label' => 'Архивные', 'icon' => 'toggle-off', 'url' => ['/tree/all-categories','in_archive' => true],],
                                    ],
                                ],
                                ['label' => 'Проекты', 'icon' => 'file-text',
                                    'items' => [
                                        ['label' => 'Актуальные', 'icon' => 'toggle-on', 'url' => ['/projects/all-projects','outdated' => false],],
                                        ['label' => 'Архивные', 'icon' => 'toggle-off', 'url' => ['/projects/all-projects','outdated' => true],],
                                    ]
                                ],
                                ['label' => 'План работы', 'icon' => 'file-text',
                                    'url' => ['/plan/all-work-plan']
                                ],
                                ['label' => 'Вебинары', 'icon' => 'youtube-play',
                                    'url' => ['/webinars/all-webinars']
                                ],
                                ['label' => 'Пользователи', 'icon' => 'user', 'url' => ['/user/all-users','not_user' => true]],
                                ['label' => 'Группы пользователей', 'icon' => 'users', 'url' => ['/user/all-user-groups']],
                            ],
                        ]
                    );
                }
        ?>

    </section>

</aside>
