# Auto-save to GitHub (Optional)

This project includes a helper script to detect file changes and automatically commit + push to the `origin` remote. Use with caution — auto-committing and pushing can cause changes that are hard to revert and may push partial work without review.

Requirements:
- macOS
- Install `fswatch`: `brew install fswatch`
- Git configured and authenticated (SSH or PAT/HTTPS)

Usage:

```bash
# Run the autosave in the project directory
cd /Applications/XAMPP/xamppfiles/htdocs/PIBS
bash tools/git-autosave.sh . "Auto: changes saved"
```

Authentication:
- HTTPS with personal access token: configure a credential helper or use `gh auth login` to authenticate.
- SSH: generate a key pair with `ssh-keygen -t ed25519`, add the public key to your GitHub account settings, then change your remote to `git@github.com:FizarErlansyah/Repositori-UAS-PIBS.git`

Safety tips:
- Use the script only for small changes or in a private repository.
- Consider creating a feature branch and pushing commits there instead of directly to `main`.
