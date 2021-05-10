<?php

namespace ImiImportByProductNumber\Core\Content\ImportExport\Processing\Pipe;


use Shopware\Core\Content\ImportExport\Processing\Pipe\AbstractPipe;
use Shopware\Core\Content\ImportExport\Processing\Pipe\AbstractPipeFactory;
use Shopware\Core\Content\ImportExport\Processing\Pipe\ChainPipe;
use Shopware\Core\Content\ImportExport\Processing\Pipe\EntityPipe;
use Shopware\Core\Content\ImportExport\Processing\Pipe\KeyMappingPipe;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Core\Content\ImportExport\Aggregate\ImportExportLog\ImportExportLogEntity;
use Shopware\Core\Content\ImportExport\DataAbstractionLayer\Serializer\SerializerRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

class PipeFactory extends AbstractPipeFactory
{
    /**
     * @var DefinitionInstanceRegistry
     */
    private $definitionInstanceRegistry;

    /**
     * @var SerializerRegistry
     */
    private $serializerRegistry;

    /**
     * @var EntityRepositoryInterface
     */
    private $productRepository;

    public function __construct(DefinitionInstanceRegistry $definitionInstanceRegistry,
                                SerializerRegistry $serializerRegistry,
                                EntityRepositoryInterface $productRepository)
    {
        $this->definitionInstanceRegistry = $definitionInstanceRegistry;
        $this->serializerRegistry = $serializerRegistry;
        $this->productRepository = $productRepository;
    }

    public function create(ImportExportLogEntity $logEntity): AbstractPipe
    {
        $pipe = new ChainPipe([
            new EntityPipe($this->definitionInstanceRegistry, $this->serializerRegistry),
            new AddIdByProductNumberPipe($this->productRepository),
            new KeyMappingPipe(),
        ]);

        return $pipe;
    }

    public function supports(ImportExportLogEntity $logEntity): bool
    {
        return $logEntity->getProfile()->getSourceEntity() === ProductDefinition::ENTITY_NAME;
    }
}
