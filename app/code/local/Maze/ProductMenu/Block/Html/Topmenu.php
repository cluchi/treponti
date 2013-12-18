<?php
class Maze_ProductMenu_Block_Html_TopMenu extends Mage_Page_Block_Html_Topmenu{

    protected function _getHtml(Varien_Data_Tree_Node $menuTree, $childrenWrapClass)
    {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = is_null($parentLevel) ? 0 : $parentLevel + 1;

        $counter = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        foreach ($children as $child) {

            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $child->setClass($outermostClass);
            }

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
            $html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>'
                . $this->escapeHtml($child->getName()) . '</span></a>';

            $catId = substr($child->getId(),14,3);
            $newCarCollection = Mage::getModel('catalog/category')->load($catId);
            $newCarCollectionProducts = $newCarCollection->getProductCollection();

            $totalProducts = count($newCarCollectionProducts);
            $totalColumns = $this->getNumberOfColumns($totalProducts);
            $totalProductsPerColumn = $this->getNumberOfProductsPerColumn($totalColumns,$totalProducts);

            if($totalProducts > 0){
                $column = 0;
                $productNo = 0;
                $html .= '<div class="products-menu">';

                foreach($newCarCollectionProducts as $product){
                    $productNo++;
                    if(1 === $productNo || 1 === $productNo%$totalProductsPerColumn){
                        $column++;
                        $html .= '<ul class="level' . $column . '">';
                    }
                    $_product = Mage::getModel('catalog/product')->load($product->getId());
                    $html .= '<li>';
                    $html .= '<a href="' . $child->getUrl().'#'.$_product->getData('url_key') . '"><span>'
                        . $this->escapeHtml($_product->getName()) . '</span></a>';
                    $html .= '</li>';

                    if(0 === $productNo%$totalProductsPerColumn || ($column === $totalColumns && $productNo === $totalProducts)){
                        $html .= '</ul>';
                    }
                }

                $html .= '</div>';
            }
            $html .= '</li>';

            $counter++;
        }
        return $html;
    }

    public function getNumberOfColumns($totalProducts){
        if($totalProducts > 24){
            $numberOfColumns = 4;
        } else if($totalProducts > 10){
            $numberOfColumns = 3;
        } else if($totalProducts > 5){
            $numberOfColumns = 2;
        } else {
            $numberOfColumns = 1;
        }
        return $numberOfColumns;
    }

    public function getNumberOfProductsPerColumn($totalColumns, $totalProducts) {
        $numberOfProducts = ceil($totalProducts/$totalColumns);
        return $numberOfProducts;
    }
}