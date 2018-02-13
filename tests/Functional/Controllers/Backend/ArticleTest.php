<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Article\Article;
use Shopware\Models\Article\Repository;

/**
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class Shopware_Tests_Controllers_Backend_ArticleTest extends Enlight_Components_Test_Controller_TestCase
{
    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * Standard set up for every test - just disable auth
     */
    public function setUp()
    {
        parent::setUp();

        $this->modelManager = Shopware()->Container()->get('models');
        $this->repository = $this->modelManager->getRepository(Article::class);

        // disable auth and acl
        Shopware()->Plugins()->Backend()->Auth()->setNoAuth();
        Shopware()->Plugins()->Backend()->Auth()->setNoAcl();
    }

    /**
     * Tests whether an article cannot be overwritten by a save request that bases on outdated data. (The article in the
     * database is newer than that one the request body is based on.)
     */
    public function testSaveArticleOverwriteProtection()
    {
        // Prepare data for the test
        $articles = $this->repository->findAll();
        /** @var Article $article */
        $article = $articles[0];

        // Prepare post data for request
        $postData = [
            'id' => $article->getId(),
            'changed' => $article->getChanged()->format('c'),
        ];

        // Try to change the entity with the correct timestamp. This should work
        $this->Request()
            ->setMethod('POST')
            ->setPost($postData);

        $this->dispatch('backend/Article/save');
        self::assertTrue($this->View()->success);

        // Now use an outdated timestamp. The controller should detect this and fail.
        $postData['changed'] = '2008-08-07 18:11:31';
        $this->Request()
            ->setMethod('POST')
            ->setPost($postData);

        $this->dispatch('backend/Article/save');
        self::assertFalse($this->View()->success);
    }
}
