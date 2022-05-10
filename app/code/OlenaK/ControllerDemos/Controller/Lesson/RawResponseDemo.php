<?php

declare(strict_types=1);

namespace OlenaK\ControllerDemos\Controller\Lesson;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Raw;

class RawResponseDemo implements HttpGetActionInterface
{
    private \Magento\Framework\App\RequestInterface $request;

    private \Magento\Framework\Controller\Result\RawFactory $rawFactory;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Controller\Result\RawFactory $rawFactory
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Controller\Result\RawFactory $rawFactory
    )
    {
        $this->request = $request;
        $this->rawFactory = $rawFactory;
    }

    /**
     * @return Raw
     */
    public function execute(): Raw
    {
       return $this->rawFactory->create()
            ->setContents(
                <<< HTML
                    <html>
                        <head>
                           <title>Test routing and controllers</title>
                        </head>
                        <body>
                           <h1>First module</h1>
                           <h3>Test Json Response</h3>
                           <div>
                               <form method="get" action="/olenak-controller-demos/lesson/jsonresponsedemo">
                                   <label>
                                      <input type="text" name="vendor_name" placeholder="Put your vendor name">
                                   </label>
                                   <br><br>
                                   <label>
                                      <input type="text" name="module_name" placeholder="Put your module name">
                                   </label>
                                   <br><br>
                                   <button type="submit">Send</button>
                               </form>
                           </div>
                           <h3>Test Redirect Response</h3>
                           <div>
                               <a href="/olenak-controller-demos/lesson/redirectresponsedemo" target="_blank">Go to my github</a>
                           </div>
                           <h3>Test Forward Response</h3>
                           <div>
                               <a href="/olenak-controller-demos/lesson/forwardresponsedemo" target="_blank">Go to Json response</a>
                           </div>
                        </body>
                    </html>
                HTML
            );
    }
}
