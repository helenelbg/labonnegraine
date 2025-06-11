<?php

use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class LbgOrderList extends Module
{
    /** @var array */
    public const MODULE_HOOKS = [
        'actionOrderGridDefinitionModifier',
        'actionOrderGridQueryBuilderModifier',
    ];

    /** @var string */
    public const LBG_EXPED_FIELD_NAME = 'id_warehouse';

    public function __construct()
    {
        $this->name = 'lbgorderlist';
        $this->author = 'La Bonne Graine';
        $this->version = '1.0.0';
        $this->ps_versions_compliancy = ['min' => '1.7.7.0', 'max' => _PS_VERSION_];

        parent::__construct();

        $this->displayName = $this->l('Date d\'expédition dans liste des commandes');
        $this->description = $this->l('');
    }

    /**
     * Installer.
     *
     * @return bool
     */
    public function install()
    {
        return parent::install()
            && $this->registerHook(static::MODULE_HOOKS);
    }

    /**
     * Modifies Order list Grid.
     *
     * @return void
     */
    public function hookActionOrderGridDefinitionModifier(array $params)
    {
        /** @var \PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface $definition */
        $definition = $params['definition'];

        $definition
            ->getColumns()
            ->addAfter(
                'payment',
                (new DataColumn(static::LBG_EXPED_FIELD_NAME))
                    ->setName($this->l('Date d\'expédition'))
                    ->setOptions([
                        'field' => static::LBG_EXPED_FIELD_NAME,
                ])
            )
        ;

        $filters = $definition->getFilters();
        $filters->add((new Filter(static::LBG_EXPED_FIELD_NAME, TextType::class))
            ->setTypeOptions([
                'required' => false,
            ])
            ->setAssociatedColumn(static::LBG_EXPED_FIELD_NAME)
        );
    }

    /**
     * Handle order list queries and filters.
     *
     * @return void
     */
    public function hookActionOrderGridQueryBuilderModifier(array $params)
    {
        /** @var \Doctrine\DBAL\Query\QueryBuilder $queryBuilder */
        $queryBuilder = $params['search_query_builder'];

        /** @var \PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface */
        $searchCriteria = $params['search_criteria'];

        $queryBuilder->addSelect(
            '(SELECT IF(odt.id_warehouse=0,"Immédiate",CONCAT("Semaine ",odt.id_warehouse)) as id_warehouset FROM ps_order_detail odt WHERE odt.id_order = o.id_order LIMIT 0,1) as id_warehouse'
        );

        //$queryBuilder->leftJoin('o', _DB_PREFIX_.'order_detail', 'od', 'o.id_order = od.id_order');
        //$queryBuilder->groupBy('od.id_order');

        if (static::LBG_EXPED_FIELD_NAME === $searchCriteria->getOrderBy()) {
            $queryBuilder->orderBy(static::LBG_EXPED_FIELD_NAME, $searchCriteria->getOrderWay());
        }

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if (static::LBG_EXPED_FIELD_NAME === $filterName) {
                $queryBuilder->having(static::LBG_EXPED_FIELD_NAME.' LIKE :'.static::LBG_EXPED_FIELD_NAME);
                $queryBuilder->setParameter(static::LBG_EXPED_FIELD_NAME, '%'.$filterValue.'%');
            }
        }
    }
}
