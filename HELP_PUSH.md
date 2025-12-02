# How to push changes to GitHub

You can edit files locally. To push changes to GitHub, you need write permission on the GitHub repository (either as a collaborator on `FizarErlansyah/Repositori-UAS-PIBS`, or by pushing to your fork).

If you don't have write permission to the original repository, do the following:

1. Fork the repository on GitHub (https://github.com/FizarErlansyah/Repositori-UAS-PIBS) to your account.
2. Rename the current `origin` remote to `upstream` and add your fork as `origin`:

   ```bash
   cd /Applications/XAMPP/xamppfiles/htdocs/PIBS
   git remote rename origin upstream
   git remote add origin https://github.com/YOUR_GITHUB_USERNAME/Repositori-UAS-PIBS.git
   git push -u origin main
   ```

3. If you prefer to use SSH (recommended for ease): set up SSH keys and switch to SSH remote:

   ```bash
   # Generate SSH key (if needed)
   ssh-keygen -t ed25519 -C "your_email@example.com"

   # Add the public key to your GitHub account (https://github.com/settings/keys)

   # Update origin to SSH (example)
   git remote set-url origin git@github.com:YOUR_GITHUB_USERNAME/Repositori-UAS-PIBS.git
   git push -u origin main
   ```

4. To keep your fork up-to-date with the original repo (upstream):

   ```bash
   git fetch upstream
   git checkout main
   git merge upstream/main
   git push origin main
   ```

Authentication options:
- HTTPS: use a personal access token (PAT) with repo scope and a credential manager.
- SSH: generate a key and add to GitHub as shown above.

If you want changes to be 'automatically' saved to your GitHub fork:
- Use the provided script `tools/git-autosave.sh` and run it in the repo. It will auto-commit and push to the currently configured `origin` remote.
- Warning: auto-commits/pushes bypass review and may cause conflicts or unintended changes. Use with caution, ideally on a feature branch.

If you need, I can:
- Guide you through creating a fork and configuring remotes.
- Help with generating an SSH key and adding it to your GitHub account.
- Install `gh` CLI and optionally use it to fork the repo and authenticate.
