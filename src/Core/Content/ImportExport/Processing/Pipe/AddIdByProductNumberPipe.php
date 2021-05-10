<?php declare(strict_types=1);

namespace ImiImportByProductNumber\Core\Content\ImportExport\Processing\Pipe;

use Shopware\Core\Content\ImportExport\Processing\Mapping\MappingCollection;
use Shopware\Core\Content\ImportExport\Processing\Pipe\AbstractPipe;
use Shopware\Core\Content\ImportExport\Struct\Config;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Util\ArrayNormalizer;

class AddIdByProductNumberPipe extends AbstractPipe
{

    private $productRepository;

    public function __construct(EntityRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function in(Config $config, iterable $record): iterable
    {

        yield from $record;
    }

    public function out(Config $config, iterable $record): iterable
    {
        foreach ($record as $key => $value) {
            if ($key === 'id' && $value === null) {
                continue;
            }
            if ($key === 'productNumber') {
                yield 'id' => $this->findProductUuid($value); // fixme: DB lookup
            }
            yield $key => $value;
        }
    }

    protected function findProductUuid($productNumber)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productNumber', $productNumber));
        $productResult = $this->productRepository->search($criteria, Context::createDefaultContext())->first();

        if($productResult === null) {
            return null; // trigger generation of a new UUID
        }

        return $productResult->get('id');
    }
}
