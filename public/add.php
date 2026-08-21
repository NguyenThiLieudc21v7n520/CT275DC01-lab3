<?php

require_once __DIR__ . '/../src/bootstrap.php';

use CT275\Labs\Contact;

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $contactData = [
        'name' => $_POST['name'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'notes' => $_POST['notes'] ?? '',
        'avatar' => null
    ];

    // Upload avatar
    if (
        isset($_FILES['avatar'])
        && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        if ($_FILES['avatar']['error'] === UPLOAD_ERR_OK) {

            $allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp'
            ];

            if (
                !in_array(
                    $_FILES['avatar']['type'],
                    $allowedTypes,
                    true
                )
            ) {
                $errors['avatar'] = 'Invalid image type.';
            } else {

                $extension = pathinfo(
                    $_FILES['avatar']['name'],
                    PATHINFO_EXTENSION
                );

                $fileName = uniqid('avatar_', true) . '.' . $extension;

                $uploadDir = __DIR__ . '/uploads/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $uploadPath = $uploadDir . $fileName;

                if (
                    move_uploaded_file(
                        $_FILES['avatar']['tmp_name'],
                        $uploadPath
                    )
                ) {
                    $contactData['avatar'] =
                        '/uploads/' . $fileName;
                } else {
                    $errors['avatar'] =
                        'Failed to upload avatar.';
                }
            }

        } else {
            $errors['avatar'] = 'Failed to upload avatar.';
        }
    }

    $contact = new Contact($PDO);

    $errors = array_merge(
        $errors,
        $contact->validate($contactData)
    );

    if (empty($errors)) {

        $contact->fill($contactData);

        if ($contact->save()) {
            redirect('/');
        }
    }
}

include_once __DIR__ . '/../src/partials/header.php';
?>

<body>

  <?php include_once __DIR__ . '/../src/partials/navbar.php' ?>

  <div class="container">

    <?php
    $subtitle = 'Add your contacts here.';
    include_once __DIR__ . '/../src/partials/heading.php';
    ?>

    <div class="row">
      <div class="col-12">

        <form
          method="post"
          enctype="multipart/form-data"
          class="col-md-6 offset-md-3"
        >

          <!-- Name -->
          <div class="mb-3">

            <label for="name" class="form-label">
              Name
            </label>

            <input
              type="text"
              name="name"
              class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>"
              maxlength="255"
              id="name"
              placeholder="Enter Name"
              value="<?= isset($_POST['name']) ? html_escape($_POST['name']) : '' ?>"
            />

            <?php if (isset($errors['name'])) : ?>

              <span class="invalid-feedback">
                <strong><?= $errors['name'] ?></strong>
              </span>

            <?php endif ?>

          </div>


          <!-- Phone -->
          <div class="mb-3">

            <label for="phone" class="form-label">
              Phone Number
            </label>

            <input
              type="text"
              name="phone"
              class="form-control<?= isset($errors['phone']) ? ' is-invalid' : '' ?>"
              maxlength="255"
              id="phone"
              placeholder="Enter Phone"
              value="<?= isset($_POST['phone']) ? html_escape($_POST['phone']) : '' ?>"
            />

            <?php if (isset($errors['phone'])) : ?>

              <span class="invalid-feedback">
                <strong><?= $errors['phone'] ?></strong>
              </span>

            <?php endif ?>

          </div>


          <!-- Notes -->
          <div class="mb-3">

            <label for="notes" class="form-label">
              Notes
            </label>

            <textarea
              name="notes"
              id="notes"
              class="form-control<?= isset($errors['notes']) ? ' is-invalid' : '' ?>"
              placeholder="Enter notes (maximum character limit: 255)"
            ><?= isset($_POST['notes']) ? html_escape($_POST['notes']) : '' ?></textarea>

            <?php if (isset($errors['notes'])) : ?>

              <span class="invalid-feedback">
                <strong><?= $errors['notes'] ?></strong>
              </span>

            <?php endif ?>

          </div>


          <!-- Avatar -->
          <div class="mb-3">

            <label for="avatar" class="form-label">
              Avatar
            </label>

            <input
              type="file"
              name="avatar"
              id="avatar"
              class="form-control<?= isset($errors['avatar']) ? ' is-invalid' : '' ?>"
              accept="image/*"
            />

            <?php if (isset($errors['avatar'])) : ?>

              <span class="invalid-feedback">
                <strong><?= $errors['avatar'] ?></strong>
              </span>

            <?php endif ?>

          </div>


          <!-- Submit -->
          <button
            type="submit"
            name="submit"
            class="btn btn-primary"
          >
            Add Contact
          </button>

        </form>

      </div>
    </div>

  </div>

  <?php include_once __DIR__ . '/../src/partials/footer.php' ?>

</body>

</html>