<div class="panel panel-wide">
    <span class="auth-eyebrow">SmartEnroll · LEMS</span>
    <h1>Enrollment Application</h1>
    <p class="hero-subtitle" style="margin-bottom: 1.5rem;">
        Tell us about your child and we'll take it from there.
    </p>

    <?php if (!empty($error)): ?>
        <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="/enrollment/apply">
        <label for="student_name">Student's Full Name</label>
        <input type="text" id="student_name" name="student_name" required autofocus
               value="<?= htmlspecialchars($old['studentName'] ?? '') ?>">

        <div class="form-row">
            <div>
                <label for="date_of_birth">Date of Birth</label>
                <input type="date" id="date_of_birth" name="date_of_birth" required
                       max="<?= date('Y-m-d') ?>"
                       value="<?= htmlspecialchars($old['dateOfBirth'] ?? '') ?>">
            </div>
            <div>
                <label for="grade_level">Grade Level</label>
                <select id="grade_level" name="grade_level" required>
                    <option value="">Select...</option>
                    <?php foreach ($gradeLevels as $level): ?>
                        <option value="<?= htmlspecialchars($level) ?>"
                            <?= (($old['gradeLevel'] ?? '') === $level) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($level) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <label for="parent_name">Parent / Guardian Name</label>
        <input type="text" id="parent_name" name="parent_name" required
               value="<?= htmlspecialchars($old['parentName'] ?? '') ?>">

        <label for="contact_number">Contact Number</label>
        <input type="tel" id="contact_number" name="contact_number" required
               placeholder="e.g. 0917 123 4567"
               value="<?= htmlspecialchars($old['contactNumber'] ?? '') ?>">

        <label for="address">Home Address</label>
        <input type="text" id="address" name="address" required
               value="<?= htmlspecialchars($old['address'] ?? '') ?>">

        <button type="submit">Submit Application</button>
    </form>
</div>
