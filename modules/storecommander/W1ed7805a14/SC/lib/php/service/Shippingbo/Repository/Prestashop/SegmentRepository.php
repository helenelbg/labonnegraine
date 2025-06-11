<?php

namespace Sc\Service\Shippingbo\Repository\Prestashop;

use ScSegment;
use ScSegmentElement;

class SegmentRepository
{
    const TYPE_PENDING = 'pending';
    const TYPE_DATE = 'by_date';

    /**
     * @var \mysqli|\PDO|resource|null
     */
    protected $pdo;
    public $id;
    public $sc_agent;

    public function __construct($sc_agent)
    {
        $this->sc_agent = $sc_agent;
        $this->pdo = \Db::getInstance()->getLink();
    }

    /**
     * get shippingbo root segment id and create it if needed.
     *
     * @throws \PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public static function getRootSegment($sc_agent)
    {
        $pdo = \Db::getInstance()->getLink();
        $stmt = $pdo->prepare('SELECT * from `'._DB_PREFIX_.'sc_segment` WHERE name = :name');
        $stmt->execute([':name' => 'Shippingbo']);
        $SboRootSegment = $stmt->fetch(\PDO::FETCH_OBJ);
        if ($stmt->rowCount() === 0)
        {
            $SboRootSegment = new \ScSegment();
            $SboRootSegment->id_parent = 0;
            $SboRootSegment->name = 'Shippingbo';
            $SboRootSegment->type = 'manual';
            $SboRootSegment->access = '-catalog-';
            $SboRootSegment->description = _l('Automatically created from Shippingbo import by %s %s on %s', false, [$sc_agent->firstname, $sc_agent->lastname, date('Y-m-d H:i:s')])."\n\n";
            $SboRootSegment->add();

            return $SboRootSegment->id; // pas la meme propriété que, l'object model dans ScSegment !?
        }

        return $SboRootSegment->id_segment;
    }

    /**
     * @throws \PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public static function getPendingSegmentId($sboRootSegmentId, $sc_agent)
    {
        $pdo = \Db::getInstance()->getLink();
        $stmt = $pdo->prepare('SELECT id_segment from `'._DB_PREFIX_.'sc_segment` WHERE name = :name AND id_parent = :sbo_root_segment_id');
        $stmt->execute([':name' => _l('Pending products'), ':sbo_root_segment_id' => $sboRootSegmentId]);
        if ($stmt->rowCount() === 0)
        {
            $SboPendingSegment = new \ScSegment();
            $SboPendingSegment->id_parent = $sboRootSegmentId;
            $SboPendingSegment->name = _l('Pending products');
            $SboPendingSegment->type = 'manual';
            $SboPendingSegment->access = '-catalog-';
            $SboPendingSegment->description = _l('Automatically created from Shippingbo import by %s %s on %s', false, [$sc_agent->firstname, $sc_agent->lastname, date('Y-m-d H:i:s')])."\n\n";
            $SboPendingSegment->add();

            return $SboPendingSegment->id;
        }
        else
        {
            return $stmt->fetch(\PDO::FETCH_COLUMN);
        }
    }

    /**
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function getInstantSboSegmentId($sboRootSegmentId, $sc_agent)
    {
        $date = date('Y-m-d H:i:s');
        $SboPendingSegment = new \ScSegment();
        $SboPendingSegment->id_parent = $sboRootSegmentId;
        $SboPendingSegment->name = _l('Products from %s', false, [$date]);
        $SboPendingSegment->type = 'manual';
        $SboPendingSegment->access = '-catalog-';
        $SboPendingSegment->description = _l('Automatically created from Shippingbo import by %s %s on %s', false, [$sc_agent->firstname, $sc_agent->lastname, $date])."\n\n";
        $SboPendingSegment->add();

        return $SboPendingSegment->id;
    }

    public static function getSegmentsIds($sc_agent)
    {
        $pdo = \Db::getInstance()->getLink();
        $stmt = $pdo->prepare('SELECT id_segment from `'._DB_PREFIX_.'sc_segment` WHERE id_parent = :sbo_root_segment_id');
        $stmt->execute([':sbo_root_segment_id' => self::getRootSegment($sc_agent)]);

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function countProductsBySegmentId($id_segment)
    {
        $pdo = \Db::getInstance()->getLink();
        $stmt = $pdo->prepare('SELECT COUNT(id_element) from `'._DB_PREFIX_.'sc_segment_element` WHERE id_segment = :id_segment AND type_element="product"');
        $stmt->execute([':id_segment' => $id_segment]);

        return $stmt->fetchColumn();
    }

    /**
     * @return void
     */
    public function addProduct($product)
    {
        if (ScSegmentElement::checkInSegment($this->id, $product->id, 'product'))
        {
            return;
        }

        $stmt = $this->pdo->prepare('INSERT INTO `'._DB_PREFIX_.'sc_segment_element` (`id_segment`, `id_element`, `type_element`) VALUES (:id_segment,:id_element,:type_element)');
        $stmt->execute([
            ':id_segment' => (int) $this->id,
            ':id_element' => (int) $product->id,
            ':type_element' => 'product',
        ]);
    }

    /**
     * @throws \PrestaShopException
     */
    public static function clearSegment()
    {
        $pdo = \Db::getInstance()->getLink();
        // recuperation de tous les id segments à supprimer
        $stmtRoot = $pdo->prepare('SELECT id_segment from `'._DB_PREFIX_.'sc_segment` WHERE name = :name');
        $stmtRoot->execute([':name' => 'Shippingbo']);
        $SboRootSegment = $stmtRoot->fetch(\PDO::FETCH_COLUMN);

        // recuperation des ids des sous segments
        $stmt = $pdo->prepare('SELECT id_segment from `'._DB_PREFIX_.'sc_segment` WHERE id_parent = :id_segment_sbo_root');
        $stmt->execute([':id_segment_sbo_root' => $SboRootSegment]);
        $segmentIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        if (!$SboRootSegment)
        {
            return;
        }
        array_push($segmentIds, $SboRootSegment);

        // suppression de tous les produits contenus dans ces segments
        $stmt = $pdo->prepare('SELECT id_element from `'._DB_PREFIX_.'sc_segment_element` WHERE id_segment IN('.implode(',', $segmentIds).') AND type_element = :type_element');
        $stmt->execute([':type_element' => 'product']);

        foreach ($stmt->fetchAll(\PDO::FETCH_COLUMN) as $productId)
        {
            $product = new \Product($productId);
            if (empty($product->getCategories()))
            {
                $product->delete();
            }
        }

        // suppression des segment elements
        $stmt = $pdo->prepare('DELETE FROM `'._DB_PREFIX_.'sc_segment_element` WHERE id_segment IN('.implode(',', $segmentIds).') AND type_element = :type_element');
        $stmt->execute([':type_element' => 'product']);

        // suppression des segments
        $stmt = $pdo->prepare('DELETE FROM `'._DB_PREFIX_.'sc_segment` WHERE id_segment IN('.implode(',', $segmentIds).')');
        $stmt->execute();
    }
}
