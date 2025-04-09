export default {
    pvextra:{
        name:'pvextra',
        gtsAPITables:{
            gsProductTree2:{
                table:'gsProductTree2',
                class:'gsProductTree',
                autocomplete_field:'',
                version:23,
                type: 3,
                authenticated:false,
                groups:'',
                permitions:'',
                active:true,
                gtsAPIUniTreeClass:{
                    gsCategory: {
                        exdended_modresource:1,
                        title_field: 'pagetitle'
                    },
                    gsProduct: {
                        exdended_modresource:1,
                        title_field: 'pagetitle'
                    },
                },
                properties: {
                    actions:{
                        create:{
                            tables:{
                                gsCategory:{
                                    label:'Создать категорию продукции',
                                    parent_classes:['modResource','gsCategory'],
                                    cls: 'p-button-rounded p-button-info',
                                    form:'UniTree',
                                    add_fields: {
                                        template: {
                                            label: 'Шаблон',
                                            type: 'autocomplete',
                                            table: 'modTemplate',
                                            defaultname:'CatalogTemplate',
                                        },
                                    }
                                },
                                gsProduct:{
                                    label:'Создать продукт',
                                    parent_classes:['gsCategory'],
                                    cls: 'p-button-rounded p-button-info',
                                    form:'UniTree',
                                    add_fields: {
                                        template: {
                                            label: 'Шаблон',
                                            type: 'autocomplete',
                                            table: 'modTemplate',
                                            defaultname:'ProductTemplate',
                                        },
                                        product_type_id: {
                                            label: 'Тип продукта',
                                            type: 'autocomplete',
                                            table: 'gsProductType',
                                            default:1,
                                        },
                                    }
                                },
                            }
                        },
                        copy:{},
                        delete:{},
                    },
                    nodeclick:{
                        classes:{
                            gsCategory:{
                                tabs:{
                                    main:{
                                        type:'form',
                                        title:'Основное1',
                                        table:'gsCategory',
                                    }
                                }
                            },
                            gsProduct:{
                                table:'gsProduct',
                                test1:{
                                    if:{
                                        product_type_id:1
                                    },
                                    tabs:{
                                        main:{
                                            type:'form',
                                            title:'Основное',
                                            table:'gsProduct',
                                        },
                                        parametrs:{
                                            title:'Параметры',
                                            table:'gsProductParam',
                                            where: {
                                                "product_id": "current_id",
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    },
                    useUniTree : true,
                    extendedModResource : true,
                    rootIds: 0,
                    idField:'id',
                    parentIdField: 'parent_id',
                    parents_idsField: 'parents_ids',
                    menuindexField: 'menuindex',
                    classField: 'class',
                    isLeaf:{
                        class:'gsProduct'
                    }
                }
            },
            gsProductTree3:{
                table:'gsProductTree3',
                class:'modResource',
                autocomplete_field:'',
                version:4,
                type: 3,
                authenticated:false,
                groups:'',
                permitions:'',
                active:true,
                gtsAPIUniTreeClass:{
                    gsCategory: {
                        exdended_modresource:1,
                        title_field: 'pagetitle'
                    },
                    gsProduct: {
                        exdended_modresource:1,
                        title_field: 'pagetitle'
                    },
                    modResource: {
                        exdended_modresource:1,
                        title_field: 'pagetitle'
                    },
                },
                properties: {
                    actions:{
                        create:{
                            tables:{
                                gsCategory:{
                                    label:'Создать категорию продукции',
                                    parent_classes:['modResource','gsCategory'],
                                    cls: 'p-button-rounded p-button-info',
                                    form:'UniTree',
                                    add_fields: {
                                        template: {
                                            label: 'Шаблон',
                                            type: 'autocomplete',
                                            table: 'modTemplate',
                                            defaultname:'CatalogTemplate',
                                        },
                                    }
                                },
                                gsProduct:{
                                    label:'Создать продукт',
                                    parent_classes:['gsCategory'],
                                    cls: 'p-button-rounded p-button-info',
                                    form:'UniTree',
                                    add_fields: {
                                        template: {
                                            label: 'Шаблон',
                                            type: 'autocomplete',
                                            table: 'modTemplate',
                                            defaultname:'ProductTemplate',
                                        },
                                        product_type_id: {
                                            label: 'Тип продукта',
                                            type: 'autocomplete',
                                            table: 'gsProductType',
                                            default:1,
                                        },
                                    }
                                },
                            }
                        },
                        copy:{},
                        delete:{},
                    },
                    nodeclick:{
                        classes:{
                            gsCategory:{
                                tabs:{
                                    main:{
                                        type:'form',
                                        title:'Основное1',
                                        table:'gsCategory',
                                    }
                                }
                            },
                            gsProduct:{
                                table:'gsProduct',
                                test1:{
                                    if:{
                                        product_type_id:1
                                    },
                                    tabs:{
                                        main:{
                                            type:'form',
                                            title:'Основное',
                                            table:'gsProduct',
                                        },
                                        parametrs:{
                                            title:'Параметры',
                                            table:'gsProductParam',
                                            where: {
                                                "product_id": "current_id",
                                            }
                                        }
                                    }
                                }
                            },
                            default:{
                                tabs:{
                                    main:{
                                        type:'form',
                                        title:'Основное1',
                                        table:'modResource',
                                    }
                                }
                            },
                        }
                    },
                    useUniTree : false,
                    extendedModResource : true,
                    rootIds: 0,
                    idField:'id',
                    parentIdField: 'parent',
                    parents_idsField: 'parents_ids',
                    menuindexField: 'menuindex',
                    classField: 'class_key',
                    titleField: 'pagetitle',
                    isLeaf:{
                        class:'gsProduct'
                    }
                }
            },
        }
    },
    gtsshop:{
        name:'gtsshop',
        gtsAPITables:{
            gsProductParam:{
                table:'gsProductParam',
                autocomplete_field:'',
                version:1,
                type: 1,
                authenticated:true,
                groups:'',
                permitions:'',
                active:true,
                properties: {
                    actions:{
                        read:{},
                        create:{},
                        update:{},
                    },
                }
            }
        }
    },
    modx:{
        name:'modx',
        gtsAPITables:{
            modTemplate:{
                table:'modTemplate',
                autocomplete_field:'template',
                version:4,
                type: 1,
                authenticated:true,
                groups:'',
                permitions:'',
                active:true,
                properties: {
                    autocomplete:{
                        tpl:'{$templatename}',
                        where:{
                            "templatename:LIKE":"%query%",
                        },
                        limit:0,
                    },
                }
            },
            modResource:{
                table:'modResource',
                autocomplete_field:'',
                version:2,
                authenticated:true,
                groups:'',
                permitions:'',
                active:true,
                properties: {
                    actions:{
                        read:{},
                        update:{}
                    },
                    "fields": {
                        "id": {
                            "type": "view",
                            "class": "modResource"
                        },
                        "pagetitle": {
                            "label":"Заголовок",
                            "type": "text",
                            "class": "modResource"
                        },
                        "alias": {
                            "label":"Псевдоним",
                            "type": "text",
                            "class": "modResource"
                        },
                        "published": {
                            "label":"Опубликовано",
                            "type": "boolean",
                            "class": "modResource"
                        },
                        "content": {
                            "label":"Содержимое",
                            "type": "textarea",
                            "class": "modResource"
                        }
                    },  
                }
            },
        }
    }
}
