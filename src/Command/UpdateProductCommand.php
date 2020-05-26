<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
use App\Service\ProductManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateProductCommand extends Command
{
    private $_pm;
    protected $overwriteFlag = true;
    protected static $defaultName = 'app:product-updater';

    // -------------------------
    public function __construct(ProductManager $pm, int $file_type = 1)
    {
        $this->_pm = $pm;
        $this->file_type = $file_type;
        parent::__construct();
    }

    // -------------------------
    protected function configure(): void
    {
        $this
            ->setDescription('Updates categories and products from files: categories.json and products.json')
            ->setHelp('Updates categories and products from files: "categories.json" and "products.json".
- categories.json file format: [ {"eId": 1, "title": "Category 1"}, ... ], 
- products.json   file format: [ {"eId": 1, "title": "Product 1", "price": 101.01, "categoriesEId": [1,2] }, ... ]')
            ->addArgument('file_type', InputArgument::REQUIRED, '1 - categories, 2 - products')
        ;
    }

    // -------------------------
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(['Products & Categories import','----------------------------','Do not panic. A lot to do...',]);
        $this->file_type = $input->getArgument('file_type');
        $output->write(['File = ', $this->file_type == 1 ? 'categories' : 'products', "\n" ]);
        $path = $path = __DIR__. DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Service' . DIRECTORY_SEPARATOR;
        $output->write(['Path = ', __DIR__, "\n"]);
        switch ($this->file_type) {
            case 1:
                $output->write("Importing categories ... ");
                $filePath1 = $path . 'categories.json';
                $this->_pm->import(Category::class, $filePath1);
                break;
            case 2:
                $output->write("Importing products ... ");
                $filePath2 = $path . 'products.json';
                $this->_pm->import(Product::class, $filePath2);
                break;
        }
        $output->writeln("OK");
        return 0;
    }
}