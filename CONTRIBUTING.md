# Contribution & Review Policy

## Pull request requirements

All pull requests must satisfy both checks below before merge:

1. **Automated validation**
   - GitHub Actions `CI / PHP tests` must pass.
   - The workflow runs the Laravel test suite automatically on every PR and on direct pushes to the protected default branch.

2. **Human review**
   - At least **one human reviewer approval** is required.
   - `Copilot` or any other AI review summary can be used as an optional reference only and must **not** be treated as a merge gate or final approval.
   - Reviewers should verify changed behavior, test coverage, and regression risk before approving.

## Recommended GitHub branch protection

Configure branch protection for `main` (or your default branch) with these options:

- Require a pull request before merging
- Require at least 1 approval
- Dismiss stale approvals when new commits are pushed
- Require review from Code Owners
- Require status checks to pass before merging
- Select the `PHP tests` job from `.github/workflows/ci.yml`
- Restrict direct pushes to protected branches

## Testing expectation

When submitting a PR, add or update tests for:

- core business logic changes
- API contract changes
- dashboard / favorites / history / training flows affected by the PR

## Local verification

Run locally before opening a PR:

```bash
composer test
```
