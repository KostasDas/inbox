<?php
namespace App\Controller;

use App\Controller\Helpers\HawkFolder;

/**
 * HawkFiles Controller
 *
 * @property \App\Model\Table\HawkFilesTable $HawkFiles
 *
 * @method \App\Model\Entity\HawkFile[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class HawkFilesController extends ApiController
{
    public function initialize()
    {


        parent::initialize(); // TODO: Change the autogenerated stub
        $this->HawkFiles->setUser($this->Auth->user());
    }

    /**
     * @param $user
     *
     * @return bool
     */
    public function isAuthorized($user)
    {
        if (in_array($this->getRequest()->getParam('action'), ['view', 'edit'])) {
            $file_id = (int)$this->getRequest()->getParam('pass.0');
            if ($this->HawkFiles->isOwnedBy($file_id, $user['id'])) {
                return true;
            }
        }
        return parent::isAuthorized($user);
    }

    /**
     *
     */
    public function index()
    {
        $files = $this->HawkFiles->find('search', ['search' => $this->getRequest()->getQueryParams()])
            ->contain(['Users'])
            ->order(['HawkFiles.created' => 'DESC']);

        $this->set(compact('files'));
        $this->set('authUser', $this->Auth->user());
    }

    /**
     * @param null $file_id
     *
     **/
    public function view($file_id = null)
    {
        $hawkFile = $this->HawkFiles->HawkUsers->find()->where([
            'hawk_file_id' => $file_id,
        ])->first();
        return $this->getResponse()->withFile($hawkFile->location);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $hawkFile = $this->HawkFiles->newEntity();
        if ($this->getRequest()->is('post')) {
            $filesSaved = false;
            $data = $this->getRequest()->getData();
            $hawkFile = $this->HawkFiles->newEntity($data);
            if ($this->HawkFiles->save($hawkFile)) {
                $filesSaved = $this->saveHawkFile($data, $hawkFile);
            }
            if ($filesSaved) {
                $this->Flash->success(__('Το αρχείο αποθηκεύτηκε με επιτυχία'));
                return $this->redirect(['action' => 'index']);
            }
            $this->HawkFiles->delete($hawkFile);
            $this->Flash->error(__('Δεν καταφέραμε να αποθηκεύσουμε το αρχείο. Παρακαλώ προσπαθήστε ξανά'));
        }
        $this->loadOptions();
        $this->set(compact('hawkFile'));
        $this->render('form');
    }

    private function saveHawkFile($data, $hawkFile)
    {
        $saved = false;
        $users = $data['user_id'];
        foreach ($users as $userId) {
            $data['user_id'] = $userId;
            $hawkFolder = new HawkFolder();
            $hawkFolder->setPath($data);
            $data['location'] = $hawkFolder->moveToProduction($data['hawk_file']);
            $saved = $this->saveHawkUsers($hawkFile->id, $userId, $data['location']);
        }
        return $saved;
    }

    private function saveHawkUsers($fileId, $userId, $location)
    {
        $entity = $this->HawkFiles->HawkUsers->newEntity([
            'user_id' => $userId,
            'hawk_file_id' => $fileId,
            'location' => $location
        ]);
        return $this->HawkFiles->HawkUsers->save($entity);
    }

    private function loadOptions()
    {
        $types = $this->HawkFiles->find('list', [
            'keyField'   => 'type',
            'valueField' => 'type',
        ])->distinct();
        $senders = $this->HawkFiles->find('list', [
            'keyField'   => 'sender',
            'valueField' => 'sender',
        ])->distinct();
        $users = $this->HawkFiles->Users->find('list', [
            'keyField'   => 'id',
            'valueField' => 'name',
        ]);

        $this->set(compact('types', 'senders', 'users'));

    }

    /**
     * Edit method
     *
     * @param string|null $id Hawk File id.
     *
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $hawkFile = $this->HawkFiles->get($id);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $data = $this->getRequest()->getData();
            $user = $this->HawkFiles->Users->get($data['user_id']);
            $previousFolder = $user->username;
            $hawkFolder = new HawkFolder(Configure::read('production_path') . DS . $user->username);
            $hawkFolder->delete($hawkFile->location);
            $data['location'] = $hawkFolder->moveToProduction(new File($data['location']['tmp_name']),
                $data['location']['name']);
            $hawkFile = $this->HawkFiles->patchEntity($hawkFile, $data);
            if (in_array('user_id', $hawkFile->getDirty())) {
                $hawkFolder = new HawkFolder(Configure::read('production_path') . DS . $previousFolder);
                $hawkFolder->deleteDir();
            }
            if ($this->HawkFiles->save($hawkFile)) {
                $this->Flash->success(__('Το αρχείο αποθηκεύτηκε με επιτυχία'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Δεν καταφέραμε να αποθηκεύσουμε το αρχείο. Παρακαλώ προσπαθήστε ξανά'));
        }
        $this->loadOptions();
        $this->set(compact('hawkFile'));
        $this->render('form');
    }

    public function types()
    {
        $types = $this->HawkFiles->find()
            ->select(['type'])
            ->order(['type' => 'ASC'])
            ->distinct()
            ->toArray();

        $this->set('types', $types);
    }

    public function senders()
    {
        $senders = $this->HawkFiles->find()
            ->select(['sender'])
            ->order(['sender' => 'ASC'])
            ->distinct()
            ->toArray();

        $this->set('senders', $senders);
    }

    public function download($file_id)
    {
        $hawkFile = $this->HawkFiles->HawkUsers->find()->where([
            'hawk_file_id' => $file_id,
        ])->first();
        return $this->getResponse()->withFile($hawkFile->location, [
            'download' => true,
        ]);
    }
}
